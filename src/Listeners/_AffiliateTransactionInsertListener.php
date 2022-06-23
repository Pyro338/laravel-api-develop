<?php

namespace Gamebetr\Api\Listeners;

use Playbetr\Core\Events\TransactionInsertEvent;
use Playbetr\SourceGroup\SourceGroup;
use Gamebetr\Api\Models\TransactionBatchQueue;

class AffiliateTransactionInsertListener
{

    public function handle(TransactionInsertEvent $event)
    {
        $transaction = $event->transaction->toArray();

        // only process debits
        if ($transaction['amount'] >= 0) {
            if (config('playbetr.affiliate.debug')) {
                \Log::debug(__FUNCTION__.' Affiliate Not a debit');
            }
            return false;
        }

        $transaction_type = 'debit';

        $config = config('playbetr.affiliate');

        if (!isset($config['propertySettings'][$transaction['property_id']])) {
            if (config('playbetr.affiliate.debug')) {
                \Log::debug(__FUNCTION__.' Affiliate No config property_id');
            }
            return false;
        }

        $settings = $config['propertySettings'][$transaction['property_id']];

        if ($settings['default_bank_id'] != $transaction['bank_id']) {
            if (config('playbetr.affiliate.debug')) {
                \Log::debug(__FUNCTION__.' Affiliate Bank ID does not match');
            }
            return false;
        }

        // look up source_group_id from source_id
        $source_group_id = SourceGroup::getSourceGroupIdBySourceId($transaction['source_id']);
        if (!$source_group_id) {
            if (config('playbetr.affiliate.debug')) {
                \Log::debug(__FUNCTION__.' Affiliate No source group ID');
            }
            return false;
        }

        // make sure handler exists otherwise it will break transactions without handler
        if (!isset($settings['handlers'][$source_group_id])) {
            if (config('playbetr.affiliate.debug')) {
                \Log::debug(__FUNCTION__.' Affiliate No handler for source group id');
            }
            return false;
        }

        $handler = $settings['handlers'][$source_group_id];

        // only process casino handlers currently (not sports or 'enabled')
        if (!preg_match('#casino#', $handler)) {
            return;
        }

        $queue_data = [
            'property_id' => $transaction['property_id'],
            'handler' => $handler,
            'type' => 'insert',
            'account_id' => $transaction['account_id'],
            'transaction_id' => $transaction['id'],
            'transaction_type' => $transaction_type,
            'transaction_amount' => $transaction['amount'],
            'source_id' => $transaction['source_id'],
            'source_group_id' => $source_group_id,
        ];
        $queue = TransactionBatchQueue::create($queue_data);
        if (config('playbetr.affiliate.debug')) {
            \Log::debug(print_r($queue->toArray(), true));
        }
    }
}
