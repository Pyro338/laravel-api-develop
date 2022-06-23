<?php

namespace Gamebetr\Api\Commands;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Exception;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Models\PaybetrApiToken;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Install extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:install
                            {--domain_id=           : The domain ID you wish to install}
                            {--hostname=            : The hostname for this domain}
                            {--paybetr_domain_id=   : The paybetr domain ID}
                            {--paybetr_domain=      : The paybetr domain you wish to use}
                            {--bank_domain=         : The domain for the bank you wish to use}
                            {--gc_domain=           : The game center domain you wish to use}
                            {--gc_encryption_key=   : The game center encryption key}
                            {--base_currency=       : The base currency for this domain}';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Complete set up for a new domain.';

    /**
     * Domain.
     * @var Domain
     */
    protected $domain;

    /**
     * Handle.
     * @return void
     */
    public function handle()
    {
        if(!$domainId = $this->option('domain_id') ?? $this->ask('Enter the domain id')) {
            return $this->error('You must supply a domain id');
        }
        // LOGIN TO THE AUTH SERVER
        if($email = $this->ask('Enter the email address')) {
            $password = $this->secret('Enter the password');
            // get auth token
            try {
                $apiToken = GlobalAuth::loginForAuthToken($domainId, $email, $password);
                $this->info('Token: '.$apiToken->api_token);
                $this->info('Token Expiration: '.$apiToken->api_token_expiration);
                $this->info('Refresh Token: '.$apiToken->refresh_token);
                $this->info('Refresh Expiration: '.$apiToken->refresh_token_expiration);
            } catch (Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        if (!$this->domain = Domain::find($domainId)) {
            return $this->error('Unknown domain');
        }
        GlobalAuth::setDomain($this->domain);
        // setup the domain host(s)
        if($hostname = $this->option('hostname') ?? $this->ask('Enter the hostname for this domain')) {
            $this->domain->hosts()->firstOrCreate([
                'host' => $hostname,
            ]);
        }
        // login to paybetr
        if($paybetrDomainId = $this->option('paybetr_domain_id') ?? $this->ask('Enter the Paybetr domain id')) {
            $paybetrEmail = $this->ask('Enter the Paybetr email');
            $paybetrPassword = $this->secret('Enter the Paybetr password');
            try {
                // get auth token
                $token = GlobalAuth::login($paybetrDomainId, $paybetrEmail, $paybetrPassword);
                $apiToken = PaybetrApiToken::firstOrNew([
                    'domain_id' => $this->domain->id,
                ]);
                $apiToken->fill([
                    'api_token' => $token->data->attributes->token,
                    'api_token_expiration' => Carbon::parse($token->data->attributes->token_expires_at),
                    'refresh_token' => $token->data->attributes->refresh_token,
                    'refresh_token_expiration' => Carbon::parse($token->data->attributes->refresh_token_expires_at),
                ]);
                $apiToken->save();
                $this->info('Token: '.$apiToken->api_token);
                $this->info('Token Expiration: '.$apiToken->api_token_expiration);
                $this->info('Refresh Token: '.$apiToken->refresh_token);
                $this->info('Refresh Expiration: '.$apiToken->refresh_token_expiration);
            } catch (Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        if($paybetrDomain = $this->option('paybetr_domain') ?? $this->ask('Enter the paybetr domain')) {
            $this->addVariable('paybetr_service_url', 'https://'.rtrim($paybetrDomain, '/').'/api/v1');
        }
        if($bankDomain = $this->option('bank_domain') ?? $this->ask('Enter the bank domain')) {
            $this->addVariable('bank_service_url', 'https://'.rtrim($bankDomain, '/').'/api/v1');
        }
        if($gcDomain = $this->option('gc_domain') ?? $this->ask('Enter the game center domain')) {
            $this->addVariable('game_center_service_url', 'https://'.rtrim($gcDomain, '/').'/api/v1');
            $this->addVariable('game_center_launch_url', 'https://'.rtrim($gcDomain, '/').'/game/launch');
        }
        if($gcEncryptionKey = $this->option('gc_encryption_key') ?? $this->ask('Enter the game center encryption key')) {
            $this->addVariable('game_center_encryption_key', $gcEncryptionKey, true);
        }
        // set up base currency
        $currencyChoices = [];
        foreach (config('api.setup_defaults.banks', []) as $currency => $currencyDetails) {
            $currencyChoices[] = $currency;
            if(!Bank::getCurrencyBySymbol($currencyDetails['display_currency'])) {
                return $this->error('Bank does not have '.$currency.' currency');
            }
        }
        $baseCurrency = $this->option('base_currency');
        if(!in_array($baseCurrency, $currencyChoices)) {
            $baseCurrency = $this->choice('Choose a base currency', $currencyChoices);
        }
        if(!$baseCurrency ?? $this->domain->variable('base_currency') || !in_array($baseCurrency, $currencyChoices)) {
            return $this->error('You must choose a base currency to continue');
        }
        $baseCurrencyDetails = config('api.setup_defaults.banks', [])[$baseCurrency];
        $this->addVariable('base_currency', $baseCurrencyDetails['display_currency']);
        // create system banks
        $this->callSilently('cache:clear');
        $systemBanks = [
            'Status Points' => ['hidden' => false, 'transferable' => false, 'relaxed' => false, 'playable' => false],
            'Betback' => ['hidden' => false, 'transferable' => true, 'relaxed' => false, 'playable' => false],
            'Lossback' => ['hidden' => false, 'transferable' => true, 'relaxed' => false, 'playable' => false],
            'Lossback Paid' => ['hidden' => true, 'transferable' => false, 'relaxed' => false, 'playable' => false],
            'Winloss' => ['hidden' => true, 'transferable' => false, 'relaxed' => true, 'playable' => false],
            'Affiliate' => ['hidden' => false, 'transferable' => true, 'relaxed' => false, 'playable' => false],
            'Migration' => ['hidden' => false, 'transferable' => true, 'relaxed' => false, 'playable' => false],
        ];
        foreach($systemBanks as $name => $details) {
            if(empty(Bank::getBanks(['filter' => ['name' => $name]])['data'])) {
                $bank = Bank::createBank(
                    $name,
                    $name.' Bank',
                    $details['hidden'],
                    $details['transferable'],
                    $details['relaxed'],
                    $details['playable'],
                    $baseCurrencyDetails['base_currency'],
                    $baseCurrencyDetails['display_currency'],
                    null
                );
                $this->addVariable(Str::snake($name).'_bank_uuid', $bank['id']);
            }
        }
        // create playable banks
        foreach (config('api.setup_defaults.banks', []) as $currency => $details) {
            if(empty(Bank::getBanks(['filter' => ['name' => $currency]])['data'])) {
                Bank::createBank(
                    $currency,
                    $currency.' Bank',
                    false,
                    false,
                    false,
                    true,
                    $details['base_currency'],
                    $details['display_currency'],
                    $details['deposit_currency']
                );
            }
        }
        // setup variables
        $this->addVariable('casino_status_points_earned', config('api.setup_defaults.vip_points_per_casino_bet'));
        $this->addVariable('sports_status_points_earned', config('api.setup_defaults.vip_points_per_sports_bet'));
        $tiers = config('api.setup_defaults.vip_tiers', []);
        $this->addVariable('vip_tiers', count($tiers));
        foreach ($tiers as $tier => $details) {
            $this->addVariable('vip_tier_'.$tier.'_name', $details['name']);
            $this->addVariable('vip_tier_'.$tier.'_points', $details['points']);
            $this->addVariable('casino_lossback_tier_'.$tier.'_percent', $details['casino_lossback']);
            $this->addVariable('sports_lossback_tier_'.$tier.'_percent', $details['sports_lossback']);
            $this->addVariable('casino_betback_tier_'.$tier.'_percent', $details['casino_betback']);
            $this->addVariable('sports_betback_tier_'.$tier.'_percent', $details['sports_betback']);
        }
        $tiers = config('api.setup_defaults.affiliate_tiers', []);
        $this->addVariable('affiliate_tiers', count($tiers));
        foreach ($tiers as $tier => $details) {
            $this->addVariable('affiliate_tier_'.$tier.'_earning_minimum', $details['earnings']);
            $this->addVariable('casino_affiliate_tier_'.$tier.'_payout', $details['casino_payout']);
            $this->addVariable('sports_affiliate_tier_'.$tier.'_payout', $details['sports_payout']);
        }
        $this->call('global-auth:list-domain-variables', ['--domain_uuid' => $this->domain->uuid]);
    }

    /**
     * Add variable.
     * @param string $variable
     * @param $value
     * @param bool $encrypted
     * @return void
     */
    protected function addVariable(string $variable, $value, bool $encrypted = false)
    {
        $this->callSilently('global-auth:add-domain-variable', [
            '--domain_uuid' => $this->domain->uuid,
            '--variable' => $variable,
            '--value' => (string) $value,
            '--encrypted' => $encrypted,
        ]);
        return;
    }
}
