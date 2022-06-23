<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\Avatar as AvatarFacade;
use Gamebetr\Api\Facades\UserSingleton;
use Gamebetr\Api\Resources\AvatarResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Avatar extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $user = UserSingleton::getUser();

        return new AvatarResource(AvatarFacade::upload($request->file('avatar'), $user));
    }
}
