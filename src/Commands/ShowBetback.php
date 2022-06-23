<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Console\Command;

class ShowBetback extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:show-betback {--domain_uuid= : Domain uuid }';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Show betback domain settings';

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
        if (!$bank = Bank::getBank($domain->variable('betback_bank_uuid'))) {
            return $this->error('Betback bank has not been setup. Please run `php artisan api:setup-betback');
        }
        if (!$currency = Bank::getBankCurrency($bank['data']['id'])) {
            return $this->error('Unable to get bank currency');
        }
        if (!$displayCurrency = Bank::getBankDisplayCurrency($bank['data']['id'])) {
            return $this->error('Unable to get bank display currency');
        }

        $this->table(null, [
            ['Betback Bank', $bank['data']['attributes']['name']],
            ['Betback Bank Currency', $currency['data']['attributes']['name']],
            ['Betback Bank Display Currency', $displayCurrency['data']['attributes']['name']],
        ]);

        $rows = [];
        if (!$tiers = $domain->variable('vip_tiers')) {
            return $this->error('Please run `php artisan api:setup-vip` first!');
        }
        for ($i = 1; $i <= $tiers; $i++) {
            $rows[] = [
                $i,
                $domain->variable('casino_betback_tier_'.$i.'_percent').'%',
                $domain->variable('sports_betback_tier_'.$i.'_percent').'%',
            ];
        }
        $this->table([
            'VIP Tier',
            'Casino Betback',
            'Sports Betback',
        ], $rows);
    }
}
