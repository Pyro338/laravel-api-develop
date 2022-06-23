<?php

namespace Gamebetr\Api\Listeners;

use Carbon\Carbon;
use Gamebetr\Api\Events\GameCenterTransactionEvent;
use Gamebetr\Api\Jobs\ProcessBetback;
use Gamebetr\Api\Jobs\ProcessLossback;
use Gamebetr\Api\Jobs\ProcessStatusPoints;
use Gamebetr\Api\Jobs\ProcessWinLoss;

class GameCenterTransactionListener
{
    /**
     * Handle the event.
     * @param \Gamebetr\Api\Events\GameCenterTransactionEvent $event
     * @return void
     */
    public function handle(GameCenterTransactionEvent $event)
    {
        $gameCenterTransaction = $event->gameCenterTransaction;
        $bankTransaction = $event->bankTransaction;
        $domainId = $gameCenterTransaction->domain_id;
        $accountId = $gameCenterTransaction->account_id;
        $amount = $bankTransaction['attributes']['amount'];
        $type = $event->type;
        $tags = $event->tags;
        $bankTransactionId = $gameCenterTransaction->bank_transaction_id;
        ProcessStatusPoints::dispatch(
            $domainId,
            $accountId,
            $amount,
            $type,
            $tags,
            $bankTransactionId
        )->delay(Carbon::now()->addSeconds(1));
        ProcessWinLoss::dispatch(
            $domainId,
            $accountId,
            $amount,
            $type,
            $tags,
            $bankTransactionId
        )->delay(Carbon::now()->addSeconds(3));
        ProcessBetback::dispatch(
            $domainId,
            $accountId,
            $amount,
            $type,
            $tags,
            $bankTransactionId
        )->delay(Carbon::now()->addSeconds(5));
        ProcessLossback::dispatch(
            $domainId,
            $accountId,
            $amount,
            $type,
            $tags,
            $bankTransactionId
        )->delay(Carbon::now()->addSeconds(10));
    }
}
