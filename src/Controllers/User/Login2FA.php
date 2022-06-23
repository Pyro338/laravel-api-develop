<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\Api;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Login2FA extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json(Api::login($request->get('email'), $request->get('password'), $request->get('key')));
    }
}
