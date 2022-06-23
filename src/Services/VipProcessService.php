<?php

namespace Gamebetr\Api\Services;

use Playbetr\AccountVariable\AccountVariable;
use Playbetr\SourceGroup\SourceGroup;
use Playbetr\TransactionBatch\TransactionBatch;
use Playbetr\Vip\Models\VipLevelsModel;
use Playbetr\Vip\Models\VipRatesModel;
use Playbetr\Core\Core;
use Playbetr\Core\Models\Account;
use Playbetr\Core\Models\Bank;
use Playbetr\NSoft\Models\NSoftTransaction;
use Playbetr\TransactionBatch\Models\WebhookLogModel;

class VipProcessService
{

    public static function diceProcess($seed, $transaction_ids, $amount_sum)
    {
        self::processDebits($seed, $transaction_ids, $amount_sum);
    }

    public static function casinoProcess($seed, $transaction_ids, $amount_sum)
    {
        self::processDebits($seed, $transaction_ids, $amount_sum);
    }

    public static function sportsProcess($data)
    {
        if (config('playbetr.vip.debug')) {
            \Log::debug('-- vip start process --');
            \Log::debug(__METHOD__.print_r($data, true));
        }

        // check if already has been processed
        $log = WebhookLogModel::where('type', 'vip')
            ->where('trigger', 'nsoft.ticket-updated')
            ->where('trigger_id', $data->id)
            ->first();
        if (config('playbetr.vip.debug')) {
            \Log::debug(__METHOD__.print_r($log, true));
        }

        // if exists do not process
        if (config('playbetr.vip.debug')) {
            \Log::debug('checking if log exists...');
        }
        if (isset($log->id)) {
            if (config('playbetr.vip.debug')) {
                \Log::debug('log exists, exit');
            }
            return;
        }
        if (config('playbetr.vip.debug')) {
            \Log::debug('log does not exist, continue...');
        }

        if (!isset($data->stake)) {
            return;
        }
        if (!isset($data->status)) {
            return;
        }
        // do not allow rejected status (or any others) to be done
        if ($data->status != 'ACCEPTED') {
            return;
        }

        // if not a completed thing exit
        if (!$data->resolution_datetime) {
            if (config('playbetr.vip.debug')) {
                \Log::debug('Datetime not resolved, exiting');
            }
            return;
        }

        // // need tags for source id
        // if (!isset($data->game_session_tags)) {
        //     return;
        // }

        // // get source_id via tags (format is source:123)
        // foreach ($tags as $tag) {
        //     if (preg_match("#source#", $tag)) {
        //         $source_chunk = explode(':', $tag);
        //         $source_id = $source_chunk[1];
        //     }
        // }

        if (!isset($data->game_session_parameters->source_id)) {
            return;
        }
        $source_id = $data->game_session_parameters->source_id;

        if (!isset($source_id)) {
            return;
        }

        // log to prevent duplicate processing
        WebhookLogModel::insert([
            'type' => 'vip',
            'trigger' => 'nsoft.ticket-updated',
            'trigger_id' => $data->id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // get source group id
        $source_group_id = SourceGroup::getSourceGroupIdBySourceId($source_id);

        // look up property_id
        $account = Account::find($data->external_id);
        $bank = Bank::find($account->bank_id);
        $property_id = $bank->property_id;

        // look up parent transaction id
        // $nsoft_transaction = NSoftTransaction::where('nsoft_reference_id', $data['ticket_hash'])->where('nsoft_transaction_type', 'reserveFunds')->first();

        // STATUS POINTS
        if (config('playbetr.vip.debug')) {
            \Log::debug('Calculating status points...');
        }
        $status_points = self::calculateStatusPoints($data->external_id, $source_group_id, $data->stake, $property_id);
        if (config('playbetr.vip.debug')) {
            \Log::debug('Status Points: '.$status_points['value']);
        }
        if ($status_points['value'] > 0) {
            $account_id_status_points = TransactionBatch::getAccountIdByBankName($data->external_id, 'Status Points', $property_id);
            $note = 'Ticket ID: '.$data->ticket_hash.' - Rate: '.$status_points['rate'];
            $transaction = Core::transaction(
                $account_id_status_points,
                $source_id,
                $status_points['value'],
                1,
                $note
            );
            if (!isset($transaction->id)) {
                if (config('playbetr.vip.debug')) {
                    \Log::debug('Error creating status points transaction');
                }
                return;
            }
        }

        // BETBACK
        if (config('playbetr.vip.debug')) {
            \Log::debug('Calculating betback...');
        }
        $betback = self::calculateRateData($data->external_id, 'betback', $source_group_id, $data->stake, $property_id);
        if (config('playbetr.vip.debug')) {
            \Log::debug('Betback: '.$betback['value']);
        }

        if ($betback['value'] > 0) {
            $account_id_betback = TransactionBatch::getAccountIdByBankName($data->external_id, 'Betback', $property_id);
            $note = 'Ticket ID: '.$data->ticket_hash.' - Rate: '.$betback['rate'];
            $transaction = Core::transaction(
                $account_id_betback,
                $source_id,
                $betback['value'],
                1,
                $note
            );
            if (!isset($transaction->id)) {
                if (config('playbetr.vip.debug')) {
                    \Log::debug('Error creating betback transaction');
                }
                return;
            }
        }

        // LOSSBACK
        // load source_ids for source_group
        $source_ids = SourceGroup::getSourceIdsBySourceGroupId($source_group_id);

        // get lossback_paid account
        $account_id_lossback_paid = TransactionBatch::getAccountIdByBankName($data->external_id, 'Lossback Paid', $property_id);

        // this requires a valid account for lossback_paid
        if (!is_numeric($account_id_lossback_paid)) {
            if (config('playbetr.vip.debug')) {
                \Log::debug('Error finding lossback paid account id');
            }
            return;
        }
        
        // load summary data
        // 2016-12-23 - changed to use $diff instead of $amount_sum for lossback
        // to ensure it doesn't pay extra in large chunks of data
        // 2019-01-22 - added ability in config to pull sum from specific date on
        // to not pull in lots of older transactions that pay out for old stuff
        $config = config('playbetr.vip');
        $settings = $config['propertySettings'][$property_id];
        $datetime = null; // default to null
        if (isset($settings['base_datetimes'][$source_group_id])) {
            $datetime = $settings['base_datetimes'][$source_group_id];
        }
        $default_bank_data = TransactionBatch::transactionSum($data->external_id, $source_ids, $datetime);
        $lossback_paid_bank_data = TransactionBatch::transactionSum($account_id_lossback_paid, $source_ids, $datetime);
        $default_winloss = $default_bank_data['win_loss'];
        $lossback_paid_floor = $lossback_paid_bank_data['win_loss'];
        if (config('playbetr.vip.debug')) {
            \Log::debug('Winloss: '.$default_winloss);
            \Log::debug('Lossback Paid: '.$lossback_paid_floor);
        }
        
        // make winloss positive and subtract lossback paid
        $diff = -$default_winloss - $lossback_paid_floor;
        if (config('playbetr.vip.debug')) {
            \Log::debug('Diff: '.$diff.' (lossback paid if diff > 0)');
        }
        if ($diff > 0) {
            if (config('playbetr.vip.debug')) {
                \Log::debug('Calculating lossback...');
            }
            $lossback = self::calculateRateData($data->external_id, 'lossback', $source_group_id, $diff, $property_id);
            if ($lossback['value'] <= 0) {
                if (config('playbetr.vip.debug')) {
                    \Log::debug('No lossback value, no lossback');
                }
                return;
            }

            // load lossback account for lossback payment
            $account_id_lossback = TransactionBatch::getAccountIdByBankName($data->external_id, 'Lossback', $property_id);
            if (!is_numeric($account_id_lossback)) {
                if (config('playbetr.vip.debug')) {
                    \Log::debug('Error finding lossback account id');
                }
                return;
            }
            $note = 'Ticket ID: '.$data->ticket_hash.' - Rate: '.$lossback['rate'];

            // pay out lossback
            $transaction = Core::transaction(
                $account_id_lossback,
                $source_id,
                $lossback['value'],
                1,
                $note
            );
            if (!isset($transaction->id)) {
                if (config('playbetr.vip.debug')) {
                    \Log::debug('Error creating lossback transaction');
                }
                return;
            }

            // insert hidden transaction for tracking totals
            $transaction2 = Core::transaction(
                $account_id_lossback_paid,
                $source_id,
                $diff,
                1,
                $note,
                $transaction->id
            );
            if (!isset($transaction2->id)) {
                if (config('playbetr.vip.debug')) {
                    \Log::debug('Error creating lossback paid transaction');
                }
                return;
            }
        }
    }

    public static function calculateStatusPoints($account_id, $source_group_id, $amount, $property_id)
    {
        // update vip level
        self::updateVipLevel($account_id, $property_id);

        $config = config('playbetr.vip');
        $settings = $config['propertySettings'][$property_id];

        $sp = 0;
        if ($multiplier = $settings['status_points_multipliers'][$source_group_id]) {
            $sp = round($amount * $multiplier, 4);
        }
        $data = [
            'value' => $sp,
            'rate' => $multiplier,
        ];
        return $data;
    }

    /**
     * Calculate a rate value for a user
     * @param $type - betback, lossback
     */
    public static function calculateRateData($account_id, $type, $source_group_id, $amount, $property_id)
    {
        $value = 0;
        $rate = 0;

        // get vip_level
        $vip_level = AccountVariable::get($account_id, 'vip_level');

        // load rate
        $rates = VipRatesModel::where('property_id', $property_id)
        ->where('type', $type)
        ->where('source_group_id', $source_group_id)
        ->where('level', $vip_level)
        ->first()
        ->toArray();
        $rate = $rates['value'];
        
        // // NEW: add in any rate modifiers if set
        // if($rate_modifier = variable_get('vip_rate_modifier_group_'.$transaction_group_id)) {
        //     $rate += $rate_modifier;
        // }

        //calc
        if ($rate > 0) {
            $value = round($amount * $rate, 4);
        }
        $data = [
            'value' => $value,
            'rate' => $rate,
        ];
        print 'Type: '.$type.' Value: '.$value.' Rate: '.$rate."\n";
        return $data;
    }

    /**
     * Used for dice and casino
     */
    public static function processDebits($seed, $transaction_ids, $amount_sum)
    {
        // only do debits
        if ($seed['transaction_type'] != 'debit') {
            return;
        }

        $config = config('playbetr.vip');
        $settings = $config['propertySettings'][$seed['property_id']];
    
        print 'Calculating status points...'."\n";
        // STATUS POINTS
        $status_points = self::calculateStatusPoints($seed['account_id'], $seed['source_group_id'], $amount_sum, $seed['property_id']);
        print 'Status Points: '.$status_points['value']."\n";
        if ($status_points['value'] > 0) {
            $account_id_status_points = TransactionBatch::getAccountIdByBankName($seed['account_id'], 'Status Points', $seed['property_id']);
            $note = min($transaction_ids).' - '.max($transaction_ids).' - Rate: '.$status_points['rate'];
            $transaction = Core::transaction(
                $account_id_status_points,
                $seed['source_id'],
                $status_points['value'],
                1,
                $note,
                min($transaction_ids)
            );
            if (!isset($transaction->id)) {
                print 'Error creating status points transaction'."\n";
                return;
            }
        }
   
        // BETBACK
        print 'Calculating betback...'."\n";
        $betback = self::calculateRateData($seed['account_id'], 'betback', $seed['source_group_id'], $amount_sum, $seed['property_id']);
        print 'Betback: '.$betback['value']."\n";
        if ($betback['value'] > 0) {
            $account_id_betback = TransactionBatch::getAccountIdByBankName($seed['account_id'], 'Betback', $seed['property_id']);
            $note = min($transaction_ids).' - '.max($transaction_ids).' - Rate: '.$betback['rate'];
            $transaction = Core::transaction(
                $account_id_betback,
                $seed['source_id'],
                $betback['value'],
                1,
                $note,
                min($transaction_ids)
            );
            if (!isset($transaction->id)) {
                print 'Error creating betback transaction'."\n";
                return;
            }
        }
    
        // LOSSBACK
        // load source_ids for source_group
        $source_ids = SourceGroup::getSourceIdsBySourceGroupId($seed['source_group_id']);

        // get lossback_paid account
        $account_id_lossback_paid = TransactionBatch::getAccountIdByBankName($seed['account_id'], 'Lossback Paid', $seed['property_id']);

        // this requires a valid account for affiliate_paid
        if (!is_numeric($account_id_lossback_paid)) {
            print 'Error finding lossback paid account id'."\n";
            return;
        }
        
        // load summary data
        // 2016-12-23 - changed to use $diff instead of $amount_sum for lossback
        // to ensure it doesn't pay extra in large chunks of data
        $default_bank_data = TransactionBatch::transactionSum($seed['account_id'], $source_ids);
        $lossback_paid_bank_data = TransactionBatch::transactionSum($account_id_lossback_paid, $source_ids);
        $default_winloss = $default_bank_data['win_loss'];
        $lossback_paid_floor = $lossback_paid_bank_data['win_loss'];
        print 'Winloss: '.$default_winloss."\n";
        print 'Lossback Paid: '.$lossback_paid_floor."\n";
        
        // make winloss positive and subtract lossback paid
        $diff = -$default_winloss - $lossback_paid_floor;
        print 'Diff: '.$diff.' (lossback paid if diff > 0)'."\n";
        if ($diff > 0) {
            print 'Calculating lossback...'."\n";
            $lossback = self::calculateRateData($seed['account_id'], 'lossback', $seed['source_group_id'], $diff, $seed['property_id']);
            if ($lossback['value'] <= 0) {
                print 'No lossback value, no lossback'."\n";
                return;
            }

            // load lossback account for lossback payment
            $account_id_lossback = TransactionBatch::getAccountIdByBankName($seed['account_id'], 'Lossback', $seed['property_id']);
            if (!is_numeric($account_id_lossback)) {
                print 'Error finding lossback account id'."\n";
                return;
            }
            $note = min($transaction_ids).' - '.max($transaction_ids).' - Rate: '.$lossback['rate'];

            // pay out lossback
            $transaction = Core::transaction(
                $account_id_lossback,
                $seed['source_id'],
                $lossback['value'],
                1,
                $note,
                min($transaction_ids)
            );
            if (!isset($transaction->id)) {
                print 'Error creating lossback transaction'."\n";
                return;
            }

            // insert hidden transaction for tracking totals
            $transaction2 = Core::transaction(
                $account_id_lossback_paid,
                $seed['source_id'],
                $diff,
                1,
                $note,
                $transaction->id
            );
            if (!isset($transaction2->id)) {
                print 'Error creating lossback paid transaction'."\n";
                return;
            }
        }
    }

    public static function updateVipLevel($account_id, $property_id)
    {
        // print 'Start VIP level update...'."\n";
        // get current sp
        $account_id_status_points = TransactionBatch::getAccountIdByBankName($account_id, 'Status Points', $property_id);
        $status_points = Core::balance($account_id_status_points);
        // print 'Status Points: '.$status_points."\n";
        
        // IMPORTANT: sometimes status points will come back null in which case
        // it will set level to 19 so it should be avoided if null
        if (!is_numeric($status_points)) {
            return;
        }
        if ($status_points < 1) {
            return;
        }

        //get current vip_level
        // $current_level = uservar_get($uid, 'vip_level');
        $current_level = AccountVariable::get($account_id, 'vip_level');
        // \Log::debug('-------------VipProcess: '.$current_level);
        // print 'Current VIP level: '.$current_level."\n";

        $level = VipLevelsModel::where('property_id', $property_id)
        ->where('status_points_required', '<=', $status_points)
        ->orderby('level', 'desc')
        ->first()
        ->toArray();
        if (!isset($level['level'])) {
            return;
        }
        $new_level = $level['level'];
        // print 'New VIP level: '.$new_level."\n";

        // update new vip level if different
        if ($new_level != $current_level) {
            AccountVariable::set($account_id, 'vip_level', $new_level);
        }
        // print 'End VIP level update'."\n";

        return 'current level: '.$current_level.'; new level: '.$new_level;
    }
}
