<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\UserSingleton;
use Gamebetr\Api\Resources\UserVariableResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListVariables extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = UserSingleton::getUser();
        
        return UserVariableResource::collection($user->variables);
    }
}
