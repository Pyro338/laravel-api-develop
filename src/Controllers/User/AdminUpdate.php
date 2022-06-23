<?php

namespace Gamebetr\Api\Controllers\User;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AdminUpdate extends Controller
{
    public function __invoke(Request $request, $uuid)
    {
        abort_unless(Auth::user()->domain_admin, JsonResponse::HTTP_FORBIDDEN, 'Unauthorized');

        $userModel = GlobalAuth::userModel();
        $user = $userModel::uuid($uuid)->first();

        abort_if(empty($user), JsonResponse::HTTP_NOT_FOUND, 'User not found');

        abort_unless($user->domain_id === Auth::user()->domain_id, JsonResponse::HTTP_NOT_FOUND, 'User not found');

        if ($request->has('affiliate_id')) {
            $user->update([
                'affiliate_id' => $request->get('affiliate_id'),
            ]);
        }

        return new UserResource(GlobalAuth::updateUser($user, $request->all()));
    }
}
