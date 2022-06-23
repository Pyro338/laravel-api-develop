<?php

namespace Gamebetr\Api\Controllers\Web;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class ConfirmReset extends Controller
{
    public function __invoke(Request $request)
    {
        $data = Cache::pull('password_recovery_'.$request->get('token'));
        abort_if(empty($data), Response::HTTP_NOT_FOUND,'This password reset link has been expired.');
        GlobalAuth::updateUser($data['user'], ['password' => decrypt($data['password'])]);
        $request->session()->flash('status', 'Your password has been reset');
        return response()->redirectTo(route('login'));
    }
}
