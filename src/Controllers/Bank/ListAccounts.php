<?php

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Models\Account;
use Gamebetr\Api\Resources\AccountResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ListAccounts extends Controller
{
    /**
     * @param Request $request
     *
     * @return AnonymousResourceCollection
     */
    public function __invoke(Request $request)
    {
        // FORCE PAGE SIZE
        if(!$request->has('page.size')) {
            $request->request->add(['page' => ['size' => 50, 'number' => 1]]);
        }

        $this->forcePlayerId($request);
        return Bank::getAccounts($request->input());

        $apiAccounts = Bank::getAccounts($request->input());
        $apiAccounts = $apiAccounts['data'] ?? $apiAccounts;
        $uuids = collect($apiAccounts)->pluck('id');

        $processed_accounts = [];

        $local_accounts = Account::whereIn('uuid', $uuids)->get();

        // Find a local account that matches this api account
        // If match, update balance and available balance of local from api
        // If no match, create local from api
        foreach ($apiAccounts as $apiAccount) {
            /** @var Account|null $matched_account */
            $matched_account = null;
            foreach ($local_accounts as $account) {
                if ($account->uuid === $apiAccount['id']) {
                    $account->balance = $apiAccount['attributes']['balance'];
                    $account->available_balance = $apiAccount['attributes']['available-balance'];
                    $matched_account = $account;
                }
            }
            $processed_accounts[] = $matched_account ?? Bank::syncAccount($apiAccount);
        }

        return AccountResource::collection($processed_accounts);
    }

    /**
     * Forces a player-id filter when necessary.
     *
     * We need to force the player-id filter in this scenario in case the
     * request came in with a player-id that does not match the current player
     * ID. If we don't do this the account mapping will fail and then try to
     * create duplicate banks.
     *
     * @param Request $request
     *  The current request.
     */
    protected function forcePlayerId(Request $request): void
    {
        // We don't need to do this for domain admins, because they may have a
        // player ID filter that doesn't match their own.
        if ($this->isDomainAdmin()) {
            return;
        }

        $filter = $request->input('filter') ?? [];
        $filter['player-id'] = (int)Auth::id();
        $request->request->set('filter', $filter);
    }

    /**
     * Check if the active user is a domain admin.
     *
     * @return bool
     *   TRUE if they are a domain admin.
     */
    protected function isDomainAdmin(): bool
    {
        return Auth::user() && !empty(Auth::user()->domain_admin);
    }

}
