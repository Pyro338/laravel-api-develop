<?php

namespace Gamebetr\Api\Listeners;

use Carbon\Carbon;
use DBD\GlobalAuth\Events\UserUpdatedEvent;
use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Jobs\CreateBankAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserUpdatedListener
{
    /**
     * Handle.
     * @param \DBD\GlobalAuth\Events\UserUpdatedEvent $event
     * @return void
     */
    public function handle(UserUpdatedEvent $event)
    {
        $user = $event->user;
        if (!$user->domain) {
            return;
        }
        Log::debug('USER UPDATED: '.json_encode($user));
        $bankUuids = Cache::remember('domain_banks_'.$user->domain->uuid, Carbon::now()->addMinutes(5), function () use ($user) {
            GlobalAuth::setDomain($user->domain);
            $bankUuids = [];
            foreach (Bank::getBanks()['data'] as $bank) {
                $bankUuids[] = $bank['id'];
            }
            return $bankUuids;
        });
        foreach ($bankUuids as $bankUuid) {
            $accounts = Bank::getAccounts([
                'filter' => [
                    'player-id' => $user->id,
                    'bank' => $bankUuid,
                ],
            ]);
            if (empty($accounts['data'])) {
                $bank = Bank::getBank($bankUuid)['data'];
                CreateBankAccount::dispatch($user->domain->id, $bank['id'], $user->id, 0, $bank['attributes']['name'], $bank['attributes']['description']);
            }
        }
    }
}
