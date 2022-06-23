<?php

namespace Gamebetr\Api\Services;

use Illuminate\Support\Facades\DB;
use Playbetr\Core\Models\TransactionSummary;
use Playbetr\Core\Models\Account;
use Playbetr\Core\Models\Bank;

class TransactionBatchService
{

    public static function transactionSum(int $account_id, array $source_ids, $from_datetime = null)
    {
        $data = TransactionSummary::select(DB::raw('
            SUM(credit_amount) as credit_amount, 
            SUM(credit_count) as credit_count,
            SUM(debit_amount) as debit_amount,
            SUM(debit_count) as debit_count,
            SUM(win_loss) as win_loss,
            SUM(transaction_count) as transaction_count
        '))
        ->where('account_id', $account_id)
        ->whereIn('source_id', $source_ids);

        // check from datetime if set
        if (!is_null($from_datetime)) {
            $data = $data->where('created_at', '>', $from_datetime);
        }

        $data = $data
        ->first()
        ->toArray();
        return $data;
    }

    /**
     * This must have accounts set up already or this will not work
     * This must have an external_id set in accounts table
     * @todo this should obviously be done in less hacky way at some point
     */
    public static function getAccountIdByBankName(int $account_id, string $bank_name, int $property_id)
    {
        // look up external_id
        if (!$account = Account::find($account_id)) {
            return;
        }
        $account = $account->toArray();
        // $account = Account::where('id', $account_id)->first()->toArray();
        // print_r($account);
        // $account1 = Account::firstOrCreate([
        //     'user_id' => $account['user_id'],
        //     'bank_id' => $bank['id'],
        //     'account' => $account['account'],
        //     'external_id' => $account['external_id'],
        // ])->toArray();

        // look up bank by name
        $bank = Bank::where('bank', $bank_name)->where('property_id', $property_id)->first()->toArray();
        // print_r($bank);

        // look up account_id of bank_id and external_id
        $account2 = Account::where('bank_id', $bank['id'])->where('external_id', $account['external_id'])->first()->toArray();

        // // if not exists create
        // $account2 = Account::firstOrCreate([
        //     'user_id' => $account['user_id'],
        //     'bank_id' => $bank['id'],
        //     'account' => $account['account'],
        //     'external_id' => $account['external_id'],
        // ])->toArray();
        // print_r($account2);

        return $account2['id'];
    }
}
