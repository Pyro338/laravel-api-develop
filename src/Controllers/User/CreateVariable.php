<?php

namespace Gamebetr\Api\Controllers\User;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\UserSingleton;
use Gamebetr\Api\Resources\UserVariableResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CreateVariable extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'variable' => 'required',
            'value' => 'required',
            'encrypted' => 'nullable',
        ]);
        $user = UserSingleton::getUser();
        $encrypted = ($request->get('encrypted') === null ? false : filter_var($request->get('encrypted'), FILTER_VALIDATE_BOOL));

        return UserVariableResource::make(GlobalAuth::addUserVariable($user, $request->get('variable'), $request->get('value'), $encrypted));
    }
}
