<?php

namespace Gamebetr\Api\Controllers\User;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\UserSingleton;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CreateApiToken extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = UserSingleton::getUser();
        $token = GlobalAuth::generateAuthToken($user->uuid);

        return response()->json($token);
    }
}
