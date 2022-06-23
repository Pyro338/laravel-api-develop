<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\UserSingleton;
use Gamebetr\Api\Resources\ReferralCollection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class AffiliateUserList extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = UserSingleton::getUser();
        $userModel = GlobalAuth::userModel();
        $userModel::addGlobalScope(new RequestScope);
        $users = $userModel::where('affiliate_id', $user->id)->get();
        return ReferralCollection::make($users);
    }
}
