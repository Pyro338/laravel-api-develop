<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Console\Command;

class ShowBanks extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:show-banks {--domain_uuid= : Domain uuid }';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Show domain banks';

    /**
     * Handle.
     * @return void
     */
    public function handle()
    {
        if (!$domainUuid = $this->option('domain_uuid')) {
            $domainChoices = [];
            foreach (Domain::all() as $domain) {
                $domainChoices[$domain->uuid] = $domain->name;
            }
            if (empty($domainChoices)) {
                return $this->error('No domains found');
            }
            $domainUuid = $this->choice('Choose a domain', $domainChoices);
        }
        if (!$domain = Domain::uuid($domainUuid)->first()) {
            return $this->error('Unknown domain');
        }
        GlobalAuth::setDomain($domain);
        $rows = [];
        foreach (Bank::getBanks()['data'] as $bank) {
            $rows[] = [
                $bank['id'],
                $bank['attributes']['name'],
                $bank['attributes']['description'],
                $bank['attributes']['hidden'],
                $bank['attributes']['transferable'],
                $bank['attributes']['relaxed-balances'],
            ];
        }
        $this->table([
            'Uuid',
            'Name',
            'Description',
            'Hidden',
            'Transferable',
            'Relaxed Balances',
        ], $rows);
    }
}
