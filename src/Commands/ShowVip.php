<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Console\Command;

class ShowVip extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:show-vip {--domain_uuid= : Domain uuid }';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Show VIP domain settings';

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
        if (!$bank = Bank::getBank($domain->variable('status_points_bank_uuid'))) {
            return $this->error('Status points bank has not been setup. Please run `php artisan api:setup-vip');
        }
        if (!$currency = Bank::getBankCurrency($bank['data']['id'])) {
            return $this->error('Unable to get bank currency');
        }
        if (!$displayCurrency = Bank::getBankDisplayCurrency($bank['data']['id'])) {
            return $this->error('Unable to get bank display currency');
        }

        $this->table(null, [
            ['Status Points Bank', $bank['data']['attributes']['name']],
            ['Status Points Bank Currency', $currency['data']['attributes']['name']],
            ['Status Points Bank Display Currency', $displayCurrency['data']['attributes']['name']],
            ['Status Points Per Casino Chip Bet', $domain->variable('casino_status_points_earned')],
            ['Status Points Per Sports Chip Bet', $domain->variable('sports_status_points_earned')],
        ]);

        $rows = [];
        if (!$tiers = $domain->variable('vip_tiers')) {
            return $this->error('Please run `php artisan api:setup-vip` first!');
        }
        for ($i = 1; $i <= $tiers; $i++) {
            $rows[] = [
                $i,
                $domain->variable('vip_tier_'.$i.'_name'),
                $domain->variable('vip_tier_'.$i.'_points'),
            ];
        }
        $this->table([
            'VIP Tier',
            'Tier Name',
            'Minimum Points',
        ], $rows);
    }
}
