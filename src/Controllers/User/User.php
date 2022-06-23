<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class User extends Controller
{
    public function __invoke(Request $request)
    {
        return new UserResource(Auth::user());
    }
}
