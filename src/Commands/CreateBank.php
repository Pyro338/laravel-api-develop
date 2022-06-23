<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Console\Command;

class CreateBank extends Command
{
    /**
     * Command signature.
     * @param string
     */
    protected $signature = 'api:create-bank
                            {--domain_uuid= : Domain uuid }
                            {--name= : Name }
                            {--description= : Description }
                            {--hidden : Hidden }
                            {--transferrable : Transferrable }
                            {--relaxed : Relaxed }
                            {--playable : Playable }
                            {--currency= : Currency }
                            {--display_currency= : Display currency }
                            {--deposit_currency= : Deposit currency }';

    /**
     * Command description.
     * @param string
     */
    protected $description = 'Create a new bank';

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
        if (!$currency = $this->option('currency')) {
            $currencyChoices = [];
            foreach (Bank::getCurrencies()['data'] as $currency) {
                if ($currency['attributes']['display-only']) {
                    continue;
                }
                $currencyChoices[] = $currency['attributes']['display-unit'];
            }
            $currency = $this->choice('Choose a currency', $currencyChoices);
        }
        if (!$currency = Bank::getCurrencyBySymbol($currency)) {
            return $this->error('Unknown currency');
        }
        if (!$displayCurrency = $this->option('display_currency')) {
            $displayCurrencyChoices = [];
            foreach (Bank::getCurrencies()['data'] as $displayCurrency) {
                if (!$displayCurrency['attributes']['display-only']) {
                    continue;
                }
                $displayCurrencyChoices[] = $displayCurrency['attributes']['display-unit'];
            }
            $displayCurrency = $this->choice('Choose a display currency', $displayCurrencyChoices);
        }
        if (!$displayCurrency = Bank::getCurrencyBySymbol($displayCurrency)) {
            return $this->error('Unknown display currency');
        }
        if (!$depositCurrency = $this->option('deposit_currency')) {
            $depositCurrency = $this->ask('Enter the deposit currency');
        }
        $name = $this->option('name') ?? $this->ask('Enter the bank name');
        $description = $this->option('description') ?? $this->ask('Enter the bank description');
        $hidden = $this->option('hidden');
        if ($hidden === null) {
            $hidden = $this->confirm('Should this bank be hidden?', false);
        }
        $transferrable = $this->option('transferrable');
        if ($transferrable === null) {
            $transferrable = $this->confirm('Should this bank be transferrable?', false);
        }
        $relaxed = $this->option('relaxed');
        if ($relaxed === null) {
            $relaxed = $this->confirm('Should this bank have relaxed balances?', false);
        }
        $playable = $this->option('playable');
        if ($playable === null) {
            $playable = $this->confirm('Should this bank be playable?', false);
        }
        $bank = Bank::createBank(
            $name,
            $description,
            $hidden,
            $transferrable,
            $relaxed,
            $playable,
            $currency['attributes']['display-unit'],
            $displayCurrency['attributes']['display-unit'],
            $depositCurrency
        );
        $this->info(json_encode($bank));
    }
}
