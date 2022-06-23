<?php

namespace Gamebetr\Api\Commands;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Models\AffiliateOverride;
use Illuminate\Console\Command;

class OverrideAffiliate extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:override-affiliate';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Override affiliate level for a user';

    /**
     * Handle.
     * @return void
     */
    public function handle()
    {
        $domainChoices = [];
        foreach (Domain::all() as $domain) {
            $domainChoices[$domain->uuid] = $domain->name;
        }
        if (empty($domainChoices)) {
            return $this->error('No domains found');
        }
        $domainUuid = $this->choice('Choose a domain', $domainChoices);
        if (!$domain = Domain::uuid($domainUuid)->first()) {
            return $this->error('Unknown domain');
        }
        $search = $this->ask('Enter a search string to find the user');
        $domainUsers = GlobalAuth::userModel()::where('domain_id', $domain->id)->where(function($query) use ($search) {
            return $query->where('name', 'like', "%$search%")->orWhere('email', 'like', "%$search%");
        })->get();
        if(empty($domainUsers)) {
            return $this->error('No users found');
        }
        $users = [];
        foreach($domainUsers as $user) {
            $users[$user->uuid] = $user->name.' <'.$user->email.'>';
        }
        $userUuid = $this->choice('Please select a user', $users);
        $user = $domain->users()->where('uuid', $userUuid)->first();
        $affiliateLevel = (int) $this->ask('Enter the affiliate level');
        $this->info($affiliateLevel);
        $affiliateOverride = AffiliateOverride::firstOrNew([
            'user_id' => $user->id,
        ]);
        $affiliateOverride->level = $affiliateLevel;
        $affiliateOverride->save();
        $this->info('Affiliate override level set');

        return 0;
    }
}
