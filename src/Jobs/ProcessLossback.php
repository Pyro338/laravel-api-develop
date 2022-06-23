<?php

namespace Gamebetr\Api\Jobs;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Exception;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Facades\Paybetr;
use Gamebetr\Api\Facades\Vip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLossback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const ALLOWED_TYPES = [
        'casino',
        'sports',
    ];

    /**
     * Domain id.
     * @var int
     */
    protected $domainId;

    /**
     * Account id.
     * @var string
     */
    protected $accountId;

    /**
     * Amount.
     * @var float
     */
    protected $amount;

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
    protected $debug = false;

    /**
     * Debug data.
     * @var array
     */
    protected $debugData = [];

    /**
     * Class constructor.
     * @param int $domainId
     * @param string $accountId
     * @param float $amount
     * @param string $type
     * @param array $tags
     * @param string $bankTransactionId
     * @return void
     */
    public function __construct(int $domainId, string $accountId, float $amount, string $type, array $tags = [], string $bankTransactionId)
    {
        $this->domainId = $domainId;
        $this->accountId = $accountId;
        $this->amount = $amount;
        $this->type = $type;
        $this->tags = $tags;
        $this->bankTransactionId = $bankTransactionId;
        $this->debug = config('api.debug_lossback', false);
        $this->debugData['init_data'] = [
            'domain_id' => $domainId,
            'account_id' => $accountId,
            'amount' => $amount,
            'type' => $type,
        ];
    }

    /**
     * Handle.
     * @return void
     */
    public function handle()
    {
        // only process debits
        if ($this->amount >= 0) {
            return;
        }
        try {
            // check for valid type
            if (!in_array($this->type, self::ALLOWED_TYPES)) {
                throw new Exception('Invalid type');
            }
            // check for valid domain
            if (!$domain = Domain::find($this->domainId)) {
                throw new Exception('No domain found');
            }
            $this->debugData['domain'] = $domain;
            GlobalAuth::setDomain($domain);
            // check for valid account
            if (!$account = Bank::getAccount($this->accountId)) {
                throw new Exception('Invalid account');
            }
            if(isset($account['data'])) {
                $account = $account['data'];
            }
            $this->debugData['original_account'] = $account;
            // check for vip level
            $vipLevel = Vip::getLevel($account['attributes']['player-id']);
            $this->debugData['vip_level'] = $vipLevel;
            // check for lossback percent
            if (!$lossbackPercent = $domain->variable($this->type . '_lossback_tier_'.$vipLevel.'_percent')) {
                $lossbackPercent = 0;
                //throw new Exception('Unknown lossback percent');
            }
            $this->debugData['lossback_percent'] = $lossbackPercent;
            // check for lossback paid account
            if (!$lossbackPaidAccount = Bank::getAccountByPlayerIdAndType($account['attributes']['player-id'], 'lossback_paid')) {
                throw new Exception('Unknown lossback paid account');
            }
            if(isset($lossbackPaidAccount['data'])) {
                $lossbackPaidAccount = $lossbackPaidAccount['data'];
            }
            $this->debugData['lossback_paid_account'] = $lossbackPaidAccount;
            // check for winloss account
            if (!$winlossAccount = Bank::getAccountByPlayerIdAndType($account['attributes']['player-id'], 'winloss')) {
                throw new Exception('Unknown winloss account');
            }
            if(isset($winlossAccount['data'])) {
                $winlossAccount = $winlossAccount['data'];
            }
            $this->debugData['winloss_account'] = $winlossAccount;
            $originalCurrency = $account['attributes']['bank']['display-currency']['display-unit'];
            $this->debugData['original_currency'] = $originalCurrency;
            $lossbackPaidCurrency = $lossbackPaidAccount['attributes']['bank']['display-currency']['display-unit'];
            $this->debugData['lossback_paid_currency'] = $lossbackPaidCurrency;
            $winlossCurrency = $winlossAccount['attributes']['bank']['display-currency']['display-unit'];
            $this->debugData['winloss_currency'] = $winlossCurrency;
            // calculate total unpaid losses
            if ($lossbackPaidCurrency == $winlossCurrency) {
                $totalUnpaidLoss = $lossbackPaidAccount['attributes']['balance'] + $winlossAccount['attributes']['balance'];
            } else {
                if (!$lossbackPaidAmount = Paybetr::convertCurrency($lossbackPaidCurrency, $winlossCurrency, $lossbackPaidAccount['attributes']['balance'])->attributes->price->price) {
                    throw new Exception('Unable to convert currency');
                }
                $totalUnpaidLoss = $lossbackPaidAmount + $winlossAccount['attributes']['balance'];
            }
            $this->debugData['total_unpaid_loss'] = $totalUnpaidLoss;
            // check if unpaid losses is greater than or equal to zero
            if ($totalUnpaidLoss >= 0) {
                $this->debugData['errors'][] = [
                    'message' => 'Player has no unpaid lossback',
                    'code' => 0,
                ];
                return $this->log();
            }
            // check for lossback account
            if (!$lossbackAccount = Bank::getAccountByPlayerIdAndType($account['attributes']['player-id'], 'lossback')) {
                throw new Exception('Unknown lossback account');
            }
            if(isset($lossbackAccount['data'])) {
                $lossbackAccount = $lossbackAccount['data'];
            }
            $this->debugData['lossback_account'] = $lossbackAccount;
            $lossbackCurrency = $lossbackAccount['attributes']['bank']['display-currency']['display-unit'];
            $this->debugData['lossback_currency'] = $lossbackCurrency;
            // get converted amount
            if ($originalCurrency == $lossbackCurrency) {
                $convertedAmount = $this->amount * -1;
            } else {
                if (!$convertedAmount = Paybetr::convertCurrency($originalCurrency, $lossbackCurrency, $this->amount)->attributes->price->price * -1) {
                    throw new Exception('Unable to convert currency');
                }
            }
            $this->debugData['converted_amount'] = $convertedAmount;
            // calculate lossback payout
            if (!$payoutAmount = $convertedAmount * ($lossbackPercent / 100)) {
                $payoutAmount = 0;
                //throw new Exception('Unable to calculate lossback payout amount');
            }
            $this->debugData['payout_amount'] = $payoutAmount;
            // calculate lossback paid amount
            if ($lossbackPaidCurrency == $lossbackCurrency) {
                $paidAmount = $convertedAmount;
            } else {
                if (!$paidAmount = Paybetr::convertCurrency($lossbackCurrency, $lossbackPaidCurrency, $convertedAmount)->attributes->price->price) {
                    throw new Exception('Unable to convert currency');
                }
            }
            $this->debugData['paid_amount'] = $paidAmount;
            // create lossback paid transaction
            $lossbackPaidTransaction = Bank::createTransaction($lossbackPaidAccount['id'], $paidAmount, 'bet', 'vip',
                [
                    'serviceCategory' => $this->type,
                    'tags' => $this->tags,
                    'parent' => $this->bankTransactionId,
                ]);
            if (!$lossbackPaidTransaction) {
                throw new Exception('Unable to create lossback paid transaction');
            }
            $this->debugData['lossback_paid_transaction'] = $lossbackPaidTransaction;
            // create lossback transaction
            $lossbackTransaction = Bank::createTransaction($lossbackAccount['id'], $payoutAmount, 'bet', 'vip',
                [
                    'serviceCategory' => $this->type,
                    'tags' => $this->tags ?? [],
                    'parent' => $this->bankTransactionId
                ]);
            if (!$lossbackTransaction) {
                throw new Exception('Unable to create lossback transaction');
            }
            $this->debugData['lossback_transaction'] = $lossbackTransaction;
            ProcessAffiliate::dispatch($account['attributes']['player-id'], $this->amount * -1, $originalCurrency, $this->type, $this->tags ?? [], $this->bankTransactionId)->delay(Carbon::now()->addSeconds(10));
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
            Log::debug('LOSSBACK: '.json_encode($this->debugData));
        }
    }
}
