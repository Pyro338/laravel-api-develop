<?php

namespace Gamebetr\Api\Jobs;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAffiliate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const ALLOWED_TYPES = [
        'casino',
        'sports',
    ];

    /**
     * Player id.
     * @var int
     */
    protected $playerId;

    /**
     * Loss Amount.
     * @var float
     */
    protected $lossAmount;

    /**
     * Currency.
     * @var string
     */
    protected $currency;

    /**
     * Type.
     * @var string
     */
    protected $type;

    /**
     * Tags.
     * @var array
     */
    protected $tags = [];

    /**
     * Bank transaction id.
     * @var string
     */
    protected $bankTransactionId;

    /**
     * Debug.
     * @var bool
     */
    protected $debug = true;

    /**
     * Debug data.
     * @var array
     */
    protected $debugData = [];

    /**
     * Class constructor.
     * @param int $playerId
     * @param float $lossAmount
     * @param string $currency
     * @param string $type
     * @param string $bankTransactionId
     * @return void
     */
    public function __construct(int $playerId, float $lossAmount, string $currency, string $type, array $tags = [], string $bankTransactionId)
    {
        $this->playerId = $playerId;
        $this->lossAmount = $lossAmount;
        $this->currency = $currency;
        $this->type = $type;
        $this->tags = $tags;
        $this->bankTransactionId = $bankTransactionId;
        $this->debug = config('api.debug_affiliate', false);
        $this->debugData['init_data'] = [
            'player_id' => $playerId,
            'loss_amount' => $lossAmount,
            'currency' => $currency,
            'type' => $type,
        ];
    }

    /**
     * Handle.
     * @return void
     */
    public function handle()
    {
        try {
            // check for valid type
            if (!in_array($this->type, self::ALLOWED_TYPES)) {
                throw new Exception('Invalid type');
            }
            // check for valid player
            if (!$player = GlobalAuth::userModel()::find($this->playerId)) {
                throw new Exception('Invalid player');
            }
            $this->debugData['player'] = $player;
            // check for affiliate
            if (!$affiliate = $player->affiliate) {
                $this->debugData['errors'][] = [
                    'message' => 'Player has no affiliate',
                    'code' => 0,
                ];
                return $this->log();
            }
            $this->debugData['affiliate'] = $affiliate;
            // check for valid domain
            if (!$domain = $player->domain) {
                throw new Exception('Invalid domain');
            }
            $this->debugData['domain'] = $domain;
            GlobalAuth::setDomain($domain);
            // check for affiliate account
            if (!$affiliateAccount = Bank::getAccountByPlayerIdAndType($affiliate->id, 'affiliate')) {
                throw new Exception('Invalid affiliate account');
            }
            if(isset($affiliateAccount['data'])) {
                $affiliateAccount = $affiliateAccount['data'];
            }
            $this->debugData['affiliate_account'] = $affiliateAccount;
            $affiliateCurrency = $affiliateAccount['attributes']['bank']['display-currency']['display-unit'];
            $this->debugData['affiliate_currency'] = $affiliateCurrency;
            // convert amount
            if ($this->currency == $affiliateCurrency) {
                $convertedAmount = $this->lossAmount;
            } else {
                if (!$convertedAmount = Paybetr::convertCurrency($this->currency, $affiliateCurrency, $this->lossAmount)->attributes->price->price) {
                    throw new Exception('Unable to convert currency');
                }
            }
            $this->debugData['converted_amount'] = $convertedAmount;
            // figure out affiliate tier
            $affiliateEarnings = Bank::getAccountWinLoss($affiliateAccount['id'], ['filter' => ['date-start' => Carbon::now()->subDays(30)->format('Y-m-d')]])['data'][0]['credit-amount'] ?? 0;
            $this->debugData['affiliate_earnings'] = $affiliateEarnings;
            $tier = 1;
            $payoutPercent = 0;
            if (!$tiers = $domain->variable('affiliate_tiers')) {
                throw new Exception('Unable to get affiliate tiers');
            }
            for ($i = 1; $i <= $tiers; $i++) {
                if ($affiliateEarnings >= $domain->variable('affiliate_tier_'.$i.'_earning_minimum')) {
                    $tier = $i;
                    $payoutPercent = $domain->variable($this->type.'_affiliate_tier_'.$i.'_payout');
                }
            }
            if($affiliate->affiliateOverride) {
                $tier = $affiliate->affiliateOverride->level;
                $payoutPercent = $domain->variable($this->type.'_affiliate_tier_'.$tier.'_payout');
            }
            $this->debugData['affiliate_tier'] = $tier;
            $this->debugData['payout_percent'] = $payoutPercent;
            $payoutAmount = $convertedAmount * ($payoutPercent / 100);
            $this->debugData['payout_amount'] = $payoutAmount;
            $affiliateTransaction = Bank::createTransaction($affiliateAccount['id'], $payoutAmount, 'bet', 'affiliate',
                [
                    'serviceCategory' => $this->type,
                    'tags' => $this->tags ?? [],
                    'parent' => $this->bankTransactionId,
                ]);
            if (!$affiliateTransaction) {
                throw new Exception('Unable to create affiliate transaction');
            }
            $this->debugData['affiliate_transaction'] = $affiliateTransaction;
        } catch (Exception $e) {
            $this->debugData['errors'][] = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
            $this->debug = true;
        }
        return $this->log();
    }

    /**
     * Log.
     * @return void
     */
    public function log()
    {
        if ($this->debug) {
            Log::debug('AFFILIATE: '.json_encode($this->debugData));
        }
    }
}
