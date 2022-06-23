<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Jobs\CreateBankAccount;
use Illuminate\Console\Command;

class SyncAccounts extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:sync-accounts {domain_id?} {bank_uuid?}';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Make sure every user has an account for the bank.';

    /**
     * Handle
     * @return void
     */
    public function handle()
    {
        if ($domainId = $this->argument('domain_id')) {
            $domain = Domain::find($domainId);
        } else {
            $domains = [];
            foreach (Domain::all() as $domain) {
                $domains[$domain->uuid] = $domain->name;
            }
            if (empty($domains)) {
                return $this->error('No domains exist in the database.');
            }
            $domainUuid = $this->choice('Choose a domain', $domains);
            $domain = Domain::uuid($domainUuid)->first();
        }
        if (!$domain) {
            return $this->error('Unknown domain');
        }
        if (!$bankUuid = $this->argument('bank_uuid')) {
            GlobalAuth::setDomain($domain);
            $banks = Bank::getBanks();
            $bankChoices = [];
            foreach ($banks['data'] as $bank) {
                $bankChoices[$bank['id']] = $bank['attributes']['name'] ?? $bank['id'];
            }
            $bankUuid = $this->choice('Choose a bank', $bankChoices);
        }
        $bank = Bank::getBank($bankUuid);
        foreach ($domain->users as $user) {
            $this->info('syncing account for user '.$user->id);
            $accounts = Bank::getAccounts([
                'filter' => [
                    'player-id' => $user->id,
                    'bank' => $bankUuid,
                ],
            ]);
            if (empty($accounts['data'])) {
                CreateBankAccount::dispatch($domain->id, $bankUuid, $user->id, 0, $bank['data']['attributes']['name'], $bank['data']['attributes']['name'].' Account');
            }
        }
    }
}
