<?php

namespace Gamebetr\Api\Controllers\User;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\UserSingleton;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Enable2fa extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = UserSingleton::getUser();
        $twoFactor = GlobalAuth::enable2fa($user->uuid);

        return response()->json($twoFactor->data);
    }
}
