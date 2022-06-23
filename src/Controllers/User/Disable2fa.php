<?php

namespace Gamebetr\Api\Controllers\User;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\UserSingleton;
use Gamebetr\Api\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Disable2fa extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = UserSingleton::getUser();
        GlobalAuth::disable2fa($user->uuid);
        $user->refresh();

        return new UserResource($user);
    }
}
