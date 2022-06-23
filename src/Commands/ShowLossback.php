<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Console\Command;

class ShowLossback extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:show-lossback {--domain_uuid= : Domain uuid }';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Show lossback domain settings';

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
        if (!$bank = Bank::getBank($domain->variable('lossback_bank_uuid'))) {
            return $this->error('Lossback bank has not been setup. Please run `php artisan api:setup-lossback');
        }
        if (!$currency = Bank::getBankCurrency($bank['data']['id'])) {
            return $this->error('Unable to get bank currency');
        }
        if (!$displayCurrency = Bank::getBankDisplayCurrency($bank['data']['id'])) {
            return $this->error('Unable to get bank display currency');
        }
        if (!$paidBank = Bank::getBank($domain->variable('lossback_paid_bank_uuid'))) {
            return $this->error('Lossback paid bank has not been setup. Please run `php artisan api:setup-lossback');
        }
        if (!$paidCurrency = Bank::getBankCurrency($paidBank['data']['id'])) {
            return $this->error('Unable to get paid bank currency');
        }
        if (!$paidDisplayCurrency = Bank::getBankDisplayCurrency($paidBank['data']['id'])) {
            return $this->error('Unable to get paid bank display currency');
        }
        if (!$winlossBank = Bank::getBank($domain->variable('winloss_bank_uuid'))) {
            return $this->error('Winloss bank has not been setup. Please run `php artisan api:setup-lossback');
        }
        if (!$winlossCurrency = Bank::getBankCurrency($winlossBank['data']['id'])) {
            return $this->error('Unable to get winloss bank currency');
        }
        if (!$winlossDisplayCurrency = Bank::getBankDisplayCurrency($winlossBank['data']['id'])) {
            return $this->error('Unable to get winloss bank display currency');
        }

        $this->table(null, [
            ['Lossback Bank', $bank['data']['attributes']['name']],
            ['Lossback Bank Currency', $currency['data']['attributes']['name']],
            ['Lossback Bank Display Currency', $displayCurrency['data']['attributes']['name']],
            ['Lossback Paid Bank', $paidBank['data']['attributes']['name']],
            ['Lossback Paid Bank Currency', $paidCurrency['data']['attributes']['name']],
            ['Lossback Paid Bank Display Currency', $paidDisplayCurrency['data']['attributes']['name']],
            ['Winloss Bank', $winlossBank['data']['attributes']['name']],
            ['Winloss Bank Currency', $winlossCurrency['data']['attributes']['name']],
            ['Winloss Bank Display Currency', $winlossDisplayCurrency['data']['attributes']['name']],
        ]);

        $rows = [];
        if (!$tiers = $domain->variable('vip_tiers')) {
            return $this->error('Please run `php artisan api:setup-vip` first!');
        }
        for ($i = 1; $i <= $tiers; $i++) {
            $rows[] = [
                $i,
                $domain->variable('casino_lossback_tier_'.$i.'_percent').'%',
                $domain->variable('sports_lossback_tier_'.$i.'_percent').'%',
            ];
        }
        $this->table([
            'VIP Tier',
            'Casino Lossback',
            'Sports Lossback',
        ], $rows);
    }
}
