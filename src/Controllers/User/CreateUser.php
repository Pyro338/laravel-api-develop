<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\Api;
use Gamebetr\Api\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class CreateUser extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = Api::registerUser(
            $request->get('name'),
            $request->get('email'),
            $request->get('password'),
            $request->get('affiliate')
        );
        Artisan::call('api:create-accounts-for-user', ['userId' => $user->id]);
        return new UserResource($user);
    }
}
