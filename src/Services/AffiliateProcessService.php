<?php

namespace Gamebetr\Api\Services;

use Playbetr\AccountVariable\AccountVariable;
use Playbetr\SourceGroup\SourceGroup;
use Playbetr\TransactionBatch\TransactionBatch;
use Playbetr\Affiliate\Models\AffiliateRatesModel;
use Playbetr\Core\Core;
use Playbetr\Core\Models\Account;
use Playbetr\Core\Models\Bank;
use Playbetr\Core\Models\Transaction;
use Playbetr\NSoft\Models\NSoftTransaction;
use Carbon\Carbon;
use Playbetr\TransactionBatch\Models\WebhookLogModel;

class AffiliateProcessService
{

    public static function diceProcess($seed, $transaction_ids, $amount_sum)
    {
        self::processDebits($seed, $transaction_ids, $amount_sum);
    }

    public static function casinoProcess($seed, $transaction_ids, $amount_sum)
    {
        self::processDebits($seed, $transaction_ids, $amount_sum);
    }

    /*
     [2020-06-25 20:56:14] production.DEBUG: Playbetr\Affiliate\AffiliateProcess::newSportsProcessstdClass Object
    (
        [id] => 414365
        [uuid] => 559f969a-ca70-4266-86be-1353c7642e5f
        [domain_id] => 1000008
        [user_id] => 1004486
        [player_id] => 1001260
        [game_session_id] => 3777380
        [external_id] => 785674
        [game_session_tags] => stdClass Object
            (
                [live] => live
                [sports] => sports
                [nsoft-live] => nsoft-live
                [source:144] => source:144
            )

        [game_session_parameters] => stdClass Object
            (
                [bank_id] => 2
                [private] => 0
                [source_id] => 144
                [odds_format] => decimal
            )

        [game_session_private_parameters] => Array
            (
            )

        [nsoft_transaction_id] => 614982
        [ticket_hash] => X109EELML
        [status] => REJECTED
        [resolution_status] => WON
        [channel] => WEB
        [game_type] => LIVE
        [type] => SINGLE
        [datetime] => 2020-06-25T20:46:18.000000Z
        [payment] => 1.00000000
        [stake] => 1.00000000
        [payment_tax] => 0.00000000
        [odd_value] => 2.27000000
        [max_win_amount] => 2.27000000
        [max_payout_amount] => 2.27000000
        [winnings] => 2.27000000
        [payout_tax] => 0.00000000
        [payout] => 0.00000000
        [bonus] => 0.00000000
        [resolution_datetime] => 2020-06-25T20:54:05.000000Z
        [payout_status] => 0
        [payout_datetime] =>
        [created_at] => 2020-06-25T20:49:09.000000Z
        [updated_at] => 2020-06-25T20:56:14.000000Z
    )
     */
    public static function newSportsProcess($data)
    {
        // check if already has been processed
        $log = WebhookLogModel::where('type', 'affiliate')
            ->where('trigger', 'nsoft.ticket-updated')
            ->where('trigger_id', $data->id)
            ->first();

        // if exists do not process
        if (isset($log->id)) {
            return;
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

        // IMPORTANT: if not a completed thing exit
        if (!$data->resolution_datetime) {
            return;
        }

        // IMPORTANT: only allow losses
        if ($data->resolution_status != 'LOST') {
            return;
        }

        if (!isset($data->game_session_parameters->source_id)) {
            return;
        }
        $source_id = $data->game_session_parameters->source_id;

        if (!isset($source_id)) {
            return;
        }

        // log to prevent duplicate processing
        WebhookLogModel::insert([
            'type' => 'affiliate',
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

        // user must have a valid affiliate
        $affiliate_account_id = AccountVariable::get($data->external_id, 'affiliate_account_id');
        if ($affiliate_account_id <= 0) {
            return;
        }

        // load source_ids for source_group
        $source_ids = SourceGroup::getSourceIdsBySourceGroupId($source_group_id);

        // NEW 2020-06-26: Only allow payouts every 24 hours or so. To do this check if txn
        // exists for affiliate in last 24 hours. This gives time for credits to catch
        // up with many bets and balance it out so it doesn't quickly pay out on like
        // 10 negative debits before the results have come back.
        $last_datetime = Carbon::now()->subDay();
        $affiliate_last_transaction = Transaction::where('account_id', $affiliate_account_id)->whereIn('source_id', $source_ids)->where('created_at', '>', $last_datetime)->orderBy('created_at', 'DESC')->take(1)->get()->toArray();

        // if id exists, return
        if (isset($affiliate_last_transaction[0]['id'])) {
            return;
        }

        // load summary data
        // NOTE: account_ids must exist on API, be sure client sites create them ahead of time
        $account_id_affiliate_paid = TransactionBatch::getAccountIdByBankName($data->external_id, 'Affiliate Paid', $property_id);

        // this requires a valid account for affiliate_paid
        if (!is_numeric($account_id_affiliate_paid)) {
            return;
        }

        // 2019-01-22 - added ability in config to pull sum from specific date on
        // to not pull in lots of older transactions that pay out for old stuff
        $config = config('playbetr.affiliate');
        $settings = $config['propertySettings'][$property_id];
        $datetime = null; // default to null
        if (isset($settings['base_datetimes'][$source_group_id])) {
            $datetime = $settings['base_datetimes'][$source_group_id];
        }
        $default_bank_data = TransactionBatch::transactionSum($data->external_id, $source_ids, $datetime);
        // use the affiliate_paid bank to get the full amount paid on and not just the amount paid * rate
        // IMPORTANT: track affiliate_paid on non-affiliate because aff commissions should be
        // grouped per player rather than by all earnings for the aff_uid.
        $affiliate_paid_bank_data = TransactionBatch::transactionSum($account_id_affiliate_paid, $source_ids, $datetime);

        $default_winloss = $default_bank_data['win_loss'];
        $affiliate_paid_floor = $affiliate_paid_bank_data['win_loss'];

        //make winloss positive and subtract affiliate paid
        $diff = -$default_winloss - $affiliate_paid_floor;
        if ($diff > 0) {
            $commission = self::calculateCommission($affiliate_account_id, $source_group_id, $diff, $property_id);
            if ($commission['value'] <= 0) {
                return;
            }

            // $note = min($transaction_ids).' - '.max($transaction_ids).' - Rate: '.$commission['rate'].' - Account ID: '.$seed['account_id'];
            $note = 'Ticket ID: '.$data->ticket_hash.' - Rate: '.$commission['rate'].' - Account ID: '.$data->external_id;

            // load affiliate's affiliate account for commission payment
            $affiliate_account_id_affiliate = TransactionBatch::getAccountIdByBankName($affiliate_account_id, 'Affiliate', $property_id);
            if (!is_numeric($affiliate_account_id_affiliate)) {
                return;
            }

            // pay out to affiliate's affiliate account
            $transaction = Core::transaction(
                $affiliate_account_id_affiliate,
                $source_id,
                $commission['value'],
                1,
                $note
            );
            if (!isset($transaction->id)) {
                print 'Error creating affiliate commission transaction';
                return;
            }

            // insert hidden transaction for tracking totals
            // IMPORTANT - account_id should match the account_id of the earlier $affiliate_paid sum query for tracking
            $response = Core::transaction(
                $account_id_affiliate_paid,
                $source_id,
                $diff,
                1,
                $note,
                $transaction->id
            );
        }
    }

    public static function sportsProcess($data)
    {
        return;
        // \Log::debug(__METHOD__.print_r($data, true));
        if (!isset($data['stake'])) {
            return;
        }

        // get source group id
        $source_group_id = SourceGroup::getSourceGroupIdBySourceId($data['source_id']);

        // look up property_id
        $account = Account::find($data['account_id']);
        $bank = Bank::find($account['bank_id']);
        $property_id = $bank['property_id'];

        // look up parent transaction id
        $nsoft_transaction = NSoftTransaction::where('nsoft_reference_id', $data['ticket_hash'])->where('nsoft_transaction_type', 'reserveFunds')->first();

        // user must have a valid affiliate
        $affiliate_account_id = AccountVariable::get($data['account_id'], 'affiliate_account_id');
        if ($affiliate_account_id <= 0) {
            return;
        }

        // NEW: 2017-03-22 - user must be vip 2 or above to pay out to affiliate
        // to avoid being gamed by new accounts affiliating themselves
        // NEW: 2017-08-07 - also allow certain affiliates to bypass this check
        // $verified = uservar_get($aff_uid, 'affiliate_verified');
        // if($verified == 0) {
        //     $vip_level = uservar_get($seed->uid, 'vip_level');
        //     if($vip_level < variable_get('affiliate_min_vip_level', 2)) {
        //         watchdog(__FUNCTION__, $seed->uid.' level '.$vip_level.' did not pass min vip check. Aff verfified: '.$verified, [], WATCHDOG_NOTICE);
        //         return;
        //     }
        // }

        // load source_ids for source_group
        $source_ids = SourceGroup::getSourceIdsBySourceGroupId($source_group_id);

        // load summary data
        // NOTE: account_ids must exist on API, be sure client sites create them ahead of time
        $account_id_affiliate_paid = TransactionBatch::getAccountIdByBankName($data['account_id'], 'Affiliate Paid', $property_id);

        // this requires a valid account for affiliate_paid
        if (!is_numeric($account_id_affiliate_paid)) {
            return;
        }

        // 2019-01-22 - added ability in config to pull sum from specific date on
        // to not pull in lots of older transactions that pay out for old stuff
        $config = config('playbetr.affiliate');
        $settings = $config['propertySettings'][$property_id];
        $datetime = null; // default to null
        if (isset($settings['base_datetimes'][$source_group_id])) {
            $datetime = $settings['base_datetimes'][$source_group_id];
        }
        $default_bank_data = TransactionBatch::transactionSum($data['account_id'], $source_ids, $datetime);
        // use the affiliate_paid bank to get the full amount paid on and not just the amount paid * rate
        // IMPORTANT: track affiliate_paid on non-affiliate because aff commissions should be
        // grouped per player rather than by all earnings for the aff_uid.
        $affiliate_paid_bank_data = TransactionBatch::transactionSum($account_id_affiliate_paid, $source_ids, $datetime);

        $default_winloss = $default_bank_data['win_loss'];
        $affiliate_paid_floor = $affiliate_paid_bank_data['win_loss'];

        //make winloss positive and subtract affiliate paid
        $diff = -$default_winloss - $affiliate_paid_floor;
        if ($diff > 0) {
            $commission = self::calculateCommission($affiliate_account_id, $source_group_id, $diff, $property_id);
            if ($commission['value'] <= 0) {
                return;
            }

            // $note = min($transaction_ids).' - '.max($transaction_ids).' - Rate: '.$commission['rate'].' - Account ID: '.$seed['account_id'];
            $note = 'Ticket ID: '.$data['ticket_hash'].' - Rate: '.$commission['rate'].' - Account ID: '.$data['account_id'];

            // load affiliate's affiliate account for commission payment
            $affiliate_account_id_affiliate = TransactionBatch::getAccountIdByBankName($affiliate_account_id, 'Affiliate', $property_id);
            if (!is_numeric($affiliate_account_id_affiliate)) {
                return;
            }

            // pay out to affiliate's affiliate account
            $transaction = Core::transaction(
                $affiliate_account_id_affiliate,
                $data['source_id'],
                $commission['value'],
                1,
                $note,
                $nsoft_transaction['transaction_id']
            );
            if (!isset($transaction->id)) {
                print 'Error creating affiliate commission transaction';
                return;
            }

            // insert hidden transaction for tracking totals
            // IMPORTANT - account_id should match the account_id of the earlier $affiliate_paid sum query for tracking
            $response = Core::transaction(
                $account_id_affiliate_paid,
                $data['source_id'],
                $diff,
                1,
                $note,
                $transaction->id
            );
        }
    }

    /**
     * Calculate the amount of commission for a user based on their tier and the transaction_group_id
     * This will first check if affiliate is active or inactive
     * If inactive it will default to the inactive rate
     * Defaults to uservar aff_tier_gid_<gid> for looking up tier and rate for that tier
     * Can be done directly with uservar aff_rate_gid_<gid> for special cases
     * @param $uid - affiliate uid
     */
    public static function calculateCommission($affiliate_account_id, $source_group_id, $amount, $property_id)
    {
        //watchdog(__FUNCTION__, 'Called by: '.$GLOBALS['user']->uid.' for uid: '.$uid);

        // update active
        // affiliate_active_update($uid);
        
        // update the tier here
        // affiliate_tier_update($uid, $transaction_group_id);

        //get rate
        $rate = 0;

        // 2017-10-01: check if affiliate inactive
        // only run if not a verified user
        // if(uservar_get($uid, 'affiliate_inactive') == 1 && uservar_get($uid, 'affiliate_verified') == 0) {
        //     watchdog(__FUNCTION__, 'Aff: '.$uid.' inactive. Rate set to 0.05.', [], WATCHDOG_DEBUG);
        //     $rate = 0.05;
        // }
        // //if active find rate
        // else {
            //if rate exists use that rate, it means the tier was over-ridden manually
            // $check = uservar_load(['uid' => $uid, 'var' => 'aff_rate_gid_'.$transaction_group_id]);
            // if(isset($check->value)) {
            //     $rate = $check->value;
            // }
            // else {
                //get affiliate tier
                // $aff_tier = uservar_get($uid, 'aff_tier_gid_'.$transaction_group_id);
                // $affiliate_tier = AccountVariable::get($affiliate_account_id, 'affiliate_tier_source_group_id_'.$source_group_id);
                //load rate
                // $rate_load = affiliate_rate_load(array(
                //     'tier' => 1, // update in future
                //     'source_group_id' => $source_group_id,
                // ));

                // HACK: temporarily hardcode tier for specific affiliate_account_id
                // $tier = 1;
                // if($affiliate_account_id == 47579) {
                //     $tier = 5;
                // }

                // load affiliate tier
                // for now, and maybe indefinitely they are stored manually in db
                $affiliate_tier = AccountVariable::get($affiliate_account_id, 'affiliate_tier_source_group_id_'.$source_group_id);
        if (!is_numeric($affiliate_tier)) {
            $affiliate_tier = 1; // default
        }
        if ($affiliate_tier == 0) {
            $affiliate_tier = 1; // default
        }
                // print $affiliate_tier;

                $rates = AffiliateRatesModel::where('property_id', $property_id)
                    ->where('source_group_id', $source_group_id)
                    ->where('tier', $affiliate_tier)
                    ->first()
                    ->toArray();
                $rate = $rates['value'];
            // }
        // }

        $value = 0;
        //calc value
        if ($rate > 0) {
            $value = round($amount * $rate, 4);
        }
        $data = [
            'value' => $value,
            'rate' => $rate,
        ];
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

        // user must have a valid affiliate
        $affiliate_account_id = AccountVariable::get($seed['account_id'], 'affiliate_account_id');
        if ($affiliate_account_id <= 0) {
            return;
        }

        // NEW: 2017-03-22 - user must be vip 2 or above to pay out to affiliate
        // to avoid being gamed by new accounts affiliating themselves
        // NEW: 2017-08-07 - also allow certain affiliates to bypass this check
        // $verified = uservar_get($aff_uid, 'affiliate_verified');
        // if($verified == 0) {
        //     $vip_level = uservar_get($seed->uid, 'vip_level');
        //     if($vip_level < variable_get('affiliate_min_vip_level', 2)) {
        //         watchdog(__FUNCTION__, $seed->uid.' level '.$vip_level.' did not pass min vip check. Aff verfified: '.$verified, [], WATCHDOG_NOTICE);
        //         return;
        //     }
        // }

        // load source_ids for source_group
        $source_ids = SourceGroup::getSourceIdsBySourceGroupId($seed['source_group_id']);

        // load summary data
        // NOTE: account_ids must exist on API, be sure client sites create them ahead of time
        $account_id_affiliate_paid = TransactionBatch::getAccountIdByBankName($seed['account_id'], 'Affiliate Paid', $seed['property_id']);

        // this requires a valid account for affiliate_paid
        if (!is_numeric($account_id_affiliate_paid)) {
            return;
        }

        $default_bank_data = TransactionBatch::transactionSum($seed['account_id'], $source_ids);
        // use the affiliate_paid bank to get the full amount paid on and not just the amount paid * rate
        // IMPORTANT: track affiliate_paid on non-affiliate because aff commissions should be
        // grouped per player rather than by all earnings for the aff_uid.
        $affiliate_paid_bank_data = TransactionBatch::transactionSum($account_id_affiliate_paid, $source_ids);

        $default_winloss = $default_bank_data['win_loss'];
        $affiliate_paid_floor = $affiliate_paid_bank_data['win_loss'];

        //make winloss positive and subtract affiliate paid
        $diff = -$default_winloss - $affiliate_paid_floor;
        if ($diff > 0) {
            $commission = self::calculateCommission($affiliate_account_id, $seed['source_group_id'], $diff, $seed['property_id']);
            if ($commission['value'] <= 0) {
                return;
            }

            $note = min($transaction_ids).' - '.max($transaction_ids).' - Rate: '.$commission['rate'].' - Account ID: '.$seed['account_id'];

            // load affiliate's affiliate account for commission payment
            $affiliate_account_id_affiliate = TransactionBatch::getAccountIdByBankName($affiliate_account_id, 'Affiliate', $seed['property_id']);
            if (!is_numeric($affiliate_account_id_affiliate)) {
                return;
            }

            // pay out to affiliate's affiliate account
            $transaction = Core::transaction(
                $affiliate_account_id_affiliate,
                $seed['source_id'],
                $commission['value'],
                1,
                $note,
                min($transaction_ids)
            );
            if (!isset($transaction->id)) {
                print 'Error creating affiliate commission transaction';
                return;
            }

            // insert hidden transaction for tracking totals
            // IMPORTANT - account_id should match the account_id of the earlier $affiliate_paid sum query for tracking
            $response = Core::transaction(
                $account_id_affiliate_paid,
                $seed['source_id'],
                $diff,
                1,
                $note,
                $transaction->id
            );
        }
    }
}
