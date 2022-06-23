<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Prize;
use Illuminate\Console\Command;

class CreatePrize extends Command
{
    /**
     * Command signature.
     * @param string
     */
    protected $signature = 'api:create-prize
                            {--domain_uuid= : Domain uuid }
                            {--name= : Name }
                            {--description= : Description }
                            {--key_class= : Key class name }';

    /**
     * Command description.
     * @param string
     */
    protected $description = 'Create a new prize';

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
        $name = $this->option('name') ?? $this->ask('Enter the prize name');
        $description = $this->option('description') ?? $this->ask('Enter the prize description');
        $keyClassName = $this->option('key_class') ?? $this->ask('Enter the prize keyClassName');
        $prize = Prize::createPrize(
            $name,
            $description,
            $keyClassName,
        );
        $this->info(json_encode($prize));
    }
}
