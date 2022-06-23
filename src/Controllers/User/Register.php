<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\Api;
use Gamebetr\Api\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class Register extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'affiliate_id' => 'nullable|integer',
        ]);
        $user = Api::registerUser(
            $request->get('name'),
            $request->get('email'),
            $request->get('password'),
            $request->get('affiliate_id')
        );
        //Artisan::call('api:create-accounts-for-user', ['userId' => $user->id]);

        return new UserResource($user);
    }
}
