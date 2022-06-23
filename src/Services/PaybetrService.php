<?php

namespace Gamebetr\Api\Services;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Facades\Utility;
use Exception;
use Gamebetr\Api\Exceptions\PaybetrApiTokenNotFound;
use Gamebetr\Api\Exceptions\UnknownDomain;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Mail\ConfirmWithdrawal;
use Gamebetr\Api\Models\PaybetrApiToken;
use Gamebetr\Api\Models\PaybetrKey;
use Gamebetr\Api\Models\PaybetrTransaction;
use Gamebetr\Api\Models\PaybetrWithdrawal;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class PaybetrService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function getServiceDomainKey(): string
    {
        return 'paybetr';
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnknownDomain
     * @throws PaybetrApiTokenNotFound
     */
    public function getApiToken(): string
    {
        if (!$domain = GlobalAuth::getDomain()) {
            abort(422, 'Unknown domain');
        }
        if (!$apiToken = PaybetrApiToken::where('domain_id', $domain->id)->first()) {
            abort(422, 'Uknown api token');
        }
        if ($apiToken->api_token_expiration < Carbon::now()->addMinutes(10)) {
            $result = GlobalAuth::refresh($apiToken->refresh_token);
            $apiToken->update([
                'api_token' => $result->data->attributes->token,
                'api_token_expiration' => Carbon::parse($result->data->attributes->token_expires_at),
                'refresh_token' => $result->data->attributes->refresh_token,
                'refresh_token_expiration' => Carbon::parse($result->data->attributes->refresh_token_expires_at),
            ]);
        }

        return $apiToken->api_token;
    }

    /**
     * List currencies.
     */
    public function listCurrencies(array $query = [])
    {
        return $this->request('GET', 'currencies?'.http_build_query($query))->data;
    }

    /**
     * Convert currency.
     */
    public function convertCurrency($from, $to, $amount = 1)
    {
        return Cache::remember('paybetr_currency_conversion_'.$from.'_'.$to.'_'.$amount, Carbon::now()->addMinutes(5), function () use ($from, $to, $amount) {
            return $this->request('GET', 'currencies/'.$from.'/convert/'.$to.'/'.$amount);
        });
    }

    /**
     * Get price.
     * @param string $from
     * @param string $to
     * @return float
     */
    public function getPrice(string $from, string $to) {
        $price = $this->request('GET', 'currencies/'.$from.'/convert/'.$to, [], true, 5);
        return $price->attributes->price->price;
    }

    /**
     * Get currency.
     */
    public function getCurrency($symbol)
    {
        return $this->request('GET', 'currencies/'.$symbol);
    }

    /**
     * List balances
     */
    public function listBalances()
    {
        return $this->request('GET', 'balances');
    }

    /**
     * Show balance
     */
    public function showBalance($symbol)
    {
        return $this->request('GET', 'currencies/'.$symbol.'/balance');
    }

    /**
     * Create address
     */
    public function createAddress($symbol, $external_id = null, bool $refresh = false)
    {
        if($refresh) {
            Cache::forget($external_id.'_'.$symbol.'_address');
        }
        return Cache::remember($external_id.'_'.$symbol.'_address', Carbon::now()->addDays(7), function() use ($symbol, $external_id) {
            return $this->request('GET', 'currencies/'.$symbol.'/addresses/new/'.$external_id);
        });
    }

    /**
     * List addresses
     */
    public function listAddresses($external_id, $symbol = null)
    {
        if ($symbol) {
            if(!$account = Bank::getAccountByPlayerIdAndType($external_id, $symbol)) {
                abort(404, 'Unknown currency');
            }
            if(!$symbol = $account['attributes']['bank']['deposit-currency']['display-unit']) {
                abort(404, 'Unknown currency');
            }
            return $this->request('GET', 'currencies/'.$symbol.'/addresses?filter[external_id]='.$external_id);
        }
        return $this->request('GET', 'addresses?filter[external_id]='.$external_id);
    }

    /**
     * List all addresses
     */
    public function listAllAddresses(array $query = []) {
        return $this->request('GET', 'addresses?'.http_build_query($query));
    }

    /**
     * Create withdrawal
     */
    public function createWithdrawal($symbol, $address, $amount, $external_id)
    {
        $symbol = strtoupper($symbol);
        $account = Bank::getAccountByCurrency($external_id, $symbol);
        if(!$withdrawalSymbol = $account['attributes']['bank']['deposit-currency']['display-unit'] ?? null) {
            abort(422, 'Account cannot be used for withdrawals');
        }
        $convertedAmount = $amount;
        if($withdrawalSymbol != $symbol) {
            $convertedAmount = $this->convertCurrency($symbol, $withdrawalSymbol, $amount)->attributes->price->price;
        }
        $withdrawalTransaction = Bank::createTransaction($account['id'], $amount * -1, 'withdrawal', 'paybetr', ['serviceCategory' => 'crypto']);
        $withdrawal = PaybetrWithdrawal::create([
            'domain_id' => GlobalAuth::getDomain()->id,
            'player_id' => $external_id,
            'account_uuid' => $account['id'],
            'transaction_uuid' => $withdrawalTransaction['data']['id'],
            'request_currency' => $symbol,
            'converted_currency' => $account['attributes']['bank']['deposit-currency']['display-unit'],
            'address' => $address,
            'amount' => $amount,
            'converted_amount' => $convertedAmount,
        ]);
        $token = Utility::randomString(64);
        Cache::put('withdrawal_'.$token, $withdrawal, Carbon::now()->addMinutes(30));
        Mail::to($withdrawal->player->email)->send(new ConfirmWithdrawal($token, $withdrawal));

        return $withdrawal;
    }

    /**
     * Confirm withdrawal
     * @param PaybetrWithdrawal $withdrawal
     * @return void
     */
    public function confirmWithdrawal(PaybetrWithdrawal $withdrawal) 
    {
        if($withdrawal->confirmed || 
           $withdrawal->cancelled ||
           $withdrawal->approved ||
           $withdrawal->refunded ||
           $withdrawal->sent) {
           abort(422, 'Withdrawal cannot be confirmed at this time');
        }
        $withdrawal->update([
            'confirmed' => true,
        ]);

        return $withdrawal;
    }

    /**
     * Cancel withdrawal
     * @param PaybetrWithdrawal $withdrawal
     * @return void
     */
    public function cancelWithdrawal(PaybetrWithdrawal $withdrawal) 
    {
        if($withdrawal->cancelled ||
           $withdrawal->approved ||
           $withdrawal->refunded ||
           $withdrawal->sent) {
           abort(422, 'Withdrawal cannot be cancelled at this time');
        }
        $withdrawal->update([
            'cancelled' => true,
        ]);

        return $this->refundWithdrawal($withdrawal);
    }

    /**
     * Refund withdrawal
     * @param PaybetrWithdrawal $withdrawal
     * @return void
     */
    public function refundWithdrawal(PaybetrWithdrawal $withdrawal) 
    {
        if($withdrawal->approved ||
           $withdrawal->refunded ||
           $withdrawal->sent) {
           abort(422, 'Withdrawal cannot be refunded at this time');
        }
        $withdrawal->update([
            'refunded' => true,
        ]);
        $transaction = Bank::getTransaction($withdrawal->transaction_uuid);
        if(isset($transaction['data'])) {
            $transaction = $transaction['data'];
        }
        $account = Bank::getAccount($withdrawal->account_uuid);
        if(isset($account['data'])) {
            $account = $account['data'];
        }
        Bank::createTransaction($account['id'], $transaction['attributes']['amount'] * -1, 'refund', 'paybetr', ['serviceCategory' => 'crypto']);

        return $withdrawal;
    }

    /**
     * Approve withdrawal
     * @param PaybetrWithdrawal $withdrawal
     * @return void
     */
    public function approveWithdrawal(PaybetrWithdrawal $withdrawal) 
    {
        if(!$withdrawal->confirmed || 
           $withdrawal->cancelled ||
           $withdrawal->approved ||
           $withdrawal->refunded ||
           $withdrawal->sent) {
           abort(422, 'Withdrawal cannot be approved at this time');
        }
        $withdrawal->update([
            'approved' => true,
        ]);
        $paybetrWithdrawal = $this->request('GET', 'currencies/'.$withdrawal->converted_currency.'/withdrawals/request/'.$withdrawal->address.'/'.$withdrawal->converted_amount.'/'.$withdrawal->player_id);
        $withdrawal->update([
            'withdrawal_uuid' => $paybetrWithdrawal->id,
            'sent' => true,
        ]);

        return $withdrawal;
    }

    /**
     * Mark withdrawal as sent
     * @param PaybetrWithdrawal $withdrawal
     * @return void
     */
    public function markWithdrawalAsSent(PaybetrWithdrawal $withdrawal) 
    {
        if($withdrawal->sent) {
           abort(422, 'Withdrawal has already been sent');
        }
        $withdrawal->update([
            'sent' => true,
        ]);
        //$this->request('GET', 'withdrawals/'.$withdrawal->uuid.'/markassent');

        return $withdrawal;
    }

    /**
     * List withdrawals
     */
    public function listWithdrawals($external_id, $symbol = null)
    {
        if ($symbol) {
            return $this->request('GET', 'currencies/'.$symbol.'/withdrawals?filter[external_id]='.$external_id);
        }
        return $this->request('GET', 'withdrawals?filter[external_id]='.$external_id);
    }

    /**
     * Moonpay
     * @param Authenticatable $user
     * @param string $currency
     */
    public function moonpay(Authenticatable $user, string $currency) {
        $params = [];
        $account = Bank::getAccountByCurrency($user->id, $currency);
        $params = [
            'currency' => $account['attributes']['bank']['deposit-currency']['display-unit'],
            'email' => $user->email,
            'external_id' => $account['id'],
        ];

        return $this->request('POST', 'moonpay', $params);
    }

    /**
     * Sync key
     */
    public function syncKey()
    {
        $key = $this->request('POST', 'callbackurl', ['url' => URL::route('api.paybetr.callback'), 'key' => Utility::randomString(64)]);
        $user = GlobalAuth::userModel()::uuid($key->id)->first();
        $paybetrKey = PaybetrKey::firstOrNew([
            'user_id' => $user->id,
        ]);
        $paybetrKey->key = $key->attributes->key;
        $paybetrKey->save();
        return $paybetrKey;
    }

    /**
     * Process callback
     */
    public function processCallback($callbackData)
    {
        if (!isset($callbackData['payload']['attributes']['user_uuid'])) {
            $this->logCallback('error', $callbackData, 'unable to find user uuid in callback data');
            return;
        }
        if (!isset($callbackData['payload']['attributes']['external_id'])) {
            $this->logCallback('error', $callbackData, 'unable to find external id in callback data');
            return;
        }
        if (!$accountId = $callbackData['payload']['attributes']['external_id']) {
            $this->logCallback('error', $callbackData, 'unable to find external id in callback data');
            return;
        }
        if(filter_var($accountId, FILTER_VALIDATE_INT)) {
            $this->logCallback('error', $callbackData, 'old address using integer id', JsonResponse::HTTP_BAD_REQUEST);
            return;
        }
        if(!$account = Bank::getAccount($accountId)) {
            $this->logCallback('error', $callbackData, 'unable to load account from external id');
            return;
        }
        if(isset($account['data'])) {
            $account = $account['data'];
        }
        $transaction = PaybetrTransaction::firstOrNew([
            'uuid' => $callbackData['payload']['id'],
        ]);
        try {
            $convertedAmount = $callbackData['payload']['attributes']['amount'];
            if(strtoupper($callbackData['payload']['attributes']['currency']) != strtoupper($account['attributes']['bank']['display-currency']['display-unit'])) {
                $convertedAmount = $this->convertCurrency($callbackData['payload']['attributes']['currency'], $account['attributes']['bank']['display-currency']['display-unit'], $callbackData['payload']['attributes']['amount'])->attributes->price->price;
            }
            $transaction->fill([
                'domain_id' => GlobalAuth::getDomain()->id,
                'player_id' => $account['attributes']['player-id'],
                'category' => $callbackData['payload']['attributes']['category'],
                'txid' => $callbackData['payload']['attributes']['txid'],
                'recipient_address' => $callbackData['payload']['attributes']['recipient_address'],
                'currency' => $callbackData['payload']['attributes']['currency'],
                'amount' => $callbackData['payload']['attributes']['amount'],
                'converted_amount' => $convertedAmount,
                'unconfirmed' => $callbackData['payload']['attributes']['unconfirmed'],
                'confirmed' => $callbackData['payload']['attributes']['confirmed'],
                'complete' => $callbackData['payload']['attributes']['complete'],
                'external_id' => $callbackData['payload']['attributes']['external_id'],
            ]);
            if ($transaction->isDirty()) {
                $transaction->save();
                if ($transaction->confirmed && !$transaction->credited && $transaction->external_id && $transaction->category == 'receive') {
                    $transaction->credited = true;
                    $transaction->save();
                    $this->logCallback('debug', $transaction, 'paybetr transaction');
                    $bankTransaction = Bank::createTransaction($account['id'], $transaction->converted_amount, 'deposit', 'paybetr', ['serviceCategory' => 'crypto']);
                    $this->logCallback('debug', $bankTransaction, 'bank transaction');
                }
            }
        } catch (Exception $e) {
            $this->logCallback('error', $callbackData, $e->getMessage());
        }
    }

    // Log callback
    protected function logCallback($type, $data, $message = '', int $statusCode = 500) {
        if(env('DEBUG_PAYBETR', false)) {
            Log::$type('PAYBETR CALLBACK: '.json_encode([
                'type' => $type,
                'message' => $message,
                'data' => $data,
            ]));
        }

        abort_if($type == 'error', $statusCode, $message);
    }

    /**
     * {@inheritdoc}
     */
    protected function processResponse($response)
    {
        return json_decode($response, false);
    }
}
