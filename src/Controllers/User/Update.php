<?php

namespace Gamebetr\Api\Controllers\User;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\UserSingleton;
use Gamebetr\Api\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Update extends Controller
{
    public function __invoke(Request $request)
    {
        $user = UserSingleton::getUser();
        $input = $request->all();
        foreach (array_keys($input) as $key) {
            if (! in_array($key, ['name', 'email', 'password'])) {
                unset($input[$key]);
            }
        }

        return new UserResource(GlobalAuth::updateUser($user, $input));
    }
}
