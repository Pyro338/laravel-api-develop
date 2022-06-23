<?php

namespace Gamebetr\Api\Listeners;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Events\UserLoggedIn;
use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLoggedInListener implements ShouldQueue
{
    /**
     * Handle.
     * @param \Gamebetr\Api\Events\UserLoggedIn $event
     * @return void
     */
    public function handle(UserLoggedIn $event)
    {
        $user = $event->user;
        if (!$user->domain) {
            return;
        }
        GlobalAuth::setDomain($user->domain);
        $banks = Bank::getBanks();
        if(isset($banks['data'])) {
            $banks = $banks['data'];
        }
        $accounts = Bank::getAccounts(['filter' => ['player-id' => $user->id], 'include' => 'bank']);
        foreach($banks as $bank) {
            $accountExists = false;
            if(isset($accounts['included'])) {
                foreach($accounts['included'] as $included) {
                    if($included['type'] = 'banks' && $included['id'] == $bank['id']) {
                        $accountExists = true;
                    }
                }
            }
            if(!$accountExists) {
                try {
                    Bank::createAccount($bank['id'], $user->id, 0, $bank['attributes']['name'], $bank['attributes']['description']);
                } catch (Exception $e) {
                }
            }
        }
    }
}
