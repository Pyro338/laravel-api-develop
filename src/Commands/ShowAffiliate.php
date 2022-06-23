<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Console\Command;

class ShowAffiliate extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:show-affiliate {--domain_uuid= : Domain uuid }';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Show affiliate domain settings';

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
        if (!$bank = Bank::getBank($domain->variable('affiliate_bank_uuid'))) {
            return $this->error('Affiliate bank has not been setup. Please run `php artisan api:setup-affilate');
        }
        if (!$currency = Bank::getBankCurrency($bank['data']['id'])) {
            return $this->error('Unable to get bank currency');
        }
        if (!$displayCurrency = Bank::getBankDisplayCurrency($bank['data']['id'])) {
            return $this->error('Unable to get bank display currency');
        }

        $this->table(null, [
            ['Affiliate Bank', $bank['data']['attributes']['name']],
            ['Affiliate Bank Currency', $currency['data']['attributes']['name']],
            ['Affiliate Bank Display Currency', $displayCurrency['data']['attributes']['name']],
        ]);

        $rows = [];
        if (!$tiers = $domain->variable('affiliate_tiers')) {
            return $this->error('Affiliate tiers has not been setup. Please run `php artisan api:setup-affiliate` first!');
        }
        for ($i = 1; $i <= $tiers; $i++) {
            $rows[] = [
                $i,
                $domain->variable('affiliate_tier_'.$i.'_earning_minimum'),
                $domain->variable('casino_affiliate_tier_'.$i.'_payout').'%',
                $domain->variable('sports_affiliate_tier_'.$i.'_payout').'%',
            ];
        }
        $this->table([
            'Affiliate Tier',
            'Minimum Earnings',
            'Casino Payout',
            'Sports Payout',
        ], $rows);
    }
}
