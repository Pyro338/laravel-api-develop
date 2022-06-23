<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\Api;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Password extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $request->validate([
                'email' => 'required|email',
                'new_password' => 'required|min:8',
            ]);
        }
        Api::recoverPassword($request->get('email'), $request->get('new_password'));

        return response()->json([], 202);
    }
}
