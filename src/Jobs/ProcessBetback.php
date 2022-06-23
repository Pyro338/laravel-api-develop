<?php

namespace Gamebetr\Api\Jobs;

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

class ProcessBetback implements ShouldQueue
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
    protected $debug = true;

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
        $this->debug = config('api.debug_betback', false);
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
            // check that account is valid
            if (!$account = Bank::getAccount($this->accountId)) {
                throw new Exception('Invalid account');
            }
            if(isset($account['data'])) {
                $account = $account['data'];
            }
            $this->debugData['original_account'] = $account;
            // get vip level
            $vipLevel = Vip::getLevel($account['attributes']['player-id']);
            $this->debugData['vip_level'] = $vipLevel;
            if (!$betbackPercent = $domain->variable($this->type . '_betback_tier_'.$vipLevel.'_percent')) {
                throw new Exception('Unknown betback percent');
            }
            $this->debugData['betback_percent'] = $betbackPercent;
            $originalCurrency = $account['attributes']['bank']['display-currency']['display-unit'];
            $this->debugData['original_currency'] = $originalCurrency;
            if (!$betbackAccount = Bank::getAccountByPlayerIdAndType($account['attributes']['player-id'], 'betback')) {
                throw new Exception('No betback account found for player');
            }
            if(isset($betbackAccount['data'])) {
                $betbackAccount = $betbackAccount['data'];
            }
            $this->debugData['betback_account'] = $betbackAccount;
            $betbackCurrency = $betbackAccount['attributes']['bank']['display-currency']['display-unit'];
            $this->debugData['betback_currency'] = $betbackCurrency;
            if ($originalCurrency == $betbackCurrency) {
                $convertedAmount = $this->amount;
            } else {
                if (!$convertedAmount = Paybetr::convertCurrency($originalCurrency, $betbackCurrency, $this->amount)->attributes->price->price) {
                    throw new Exception('Unable to convert currency');
                }
            }
            $this->debugData['converted_amount'] = $convertedAmount;
            $amountEarned = $convertedAmount * ($betbackPercent / 100) * -1;
            $this->debugData['amount_earned'] = $amountEarned;
            $betbackTransaction = Bank::createTransaction($betbackAccount['id'], $amountEarned, 'bet', 'vip',
                [
                    'serviceCategory' => $this->type,
                    'tags' => $this->tags ?? [],
                    'parent' => $this->bankTransactionId,
                ]);
            if (!$betbackTransaction) {
                throw new Exception('Unable to create betback transaction');
            }
            $this->debugData['betback_transaction'] = $betbackTransaction;
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
            Log::debug('BETBACK: '.json_encode($this->debugData));
        }
    }
}
