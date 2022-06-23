<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Exceptions\AccessDenied;
use Gamebetr\Api\Facades\Affiliate;
use Gamebetr\Api\Resources\UserCollection;
use Gamebetr\Api\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class ListUsers extends Controller
{
    public function __invoke(Request $request)
    {
        $userModel = GlobalAuth::userModel();
        $userModel::addGlobalScope(new RequestScope);
        $requestingUser = Auth::user();
        if (!$uuid = $request->get('user_uuid')) {
            $uuid = $requestingUser->uuid;
        }
        try {
            $affiliate = new AffiliateService();
            return $affiliate->listUsers($request->all());
        }
        catch (Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }
        if ($uuid != $requestingUser->uuid && !$requestingUser->domain_admin) {
            throw new AccessDenied();
        }

        return new UserCollection(Affiliate::listUsers($userModel::whereUuid($uuid)->first()));
    }
}
