<?php

namespace Gamebetr\Api\Commands;

use App\Models\User;
use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Exception;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Jobs\CreateBankAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CreateAccountsForUser extends Command
{
    /**
     * Command signature.
     * @var string
     */
    protected $signature = 'api:create-accounts-for-user {userId?}';

    /**
     * Command description.
     * @var string
     */
    protected $description = 'Create all necessary bank accounts for a user.';

    /**
     * Handle.
     * @return void
     */
    public function handle()
    {
        if (!$userId = $this->argument('userId')) {
            $domains = [];
            foreach (Domain::all() as $domain) {
                $domains[$domain->uuid] = $domain->name;
            }
            if (empty($domains)) {
                return $this->error('No domains exist in the database.');
            }
            $domainUuid = $this->choice('Choose a domain', $domains);
            $domain = Domain::uuid($domainUuid)->first();
            if ($searchString = $this->ask('Please enter a search string to find the user')) {
                $domainUsers = $domain->users()->where(function ($query) use ($searchString) {
                    return $query->where('name', 'like', "%$searchString%")->orWhere('email', 'like', "%$searchString%");
                })->get();
            } else {
                $domainUsers = $domain->users;
            }
            $userChoice = [];
            foreach ($domainUsers as $user) {
                $userChoice[$user->uuid] = $user->name.' <'.$user->email.'>';
            }
            if (empty($userChoice)) {
                return $this->error('No users were found');
            }
            $userUuid = $this->choice('Choose a user', $userChoice);
            $user = $domain->users()->uuid($userUuid)->first();
            $userId = $user->id;
        }
        if ($userId == 'all') {
            $userModel = GlobalAuth::userModel();
            foreach ($userModel::all() as $user) {
                $this->createAccounts($user->id);
            }
        } else {
            $this->createAccounts($userId);
        }
    }

    /**
     * Create accounts.
     * @param int $userId
     */
    protected function createAccounts(int $userId)
    {
        $userModel = GlobalAuth::userModel();
        if (!$user = $userModel::find($userId)) {
            return $this->error('Unable to find user '.$userId);
        }
        GlobalAuth::setDomain($user->domain);
        $banks = Cache::remember('domain_banks_'.$user->domain->id, Carbon::now()->addMinutes(10), function() {
            return Bank::getBanks();
        });
        foreach ($banks['data'] as $bank) {
            CreateBankAccount::dispatch($user->domain->id, $bank['id'], $user->id, 0, $bank['attributes']['name'], $bank['attributes']['description']);
        }
    }
}
