<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\UserSingleton;
use Gamebetr\Api\Resources\UserVariableResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetVariable extends Controller
{
    /**
     * Invoke.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $variable
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $variable)
    {
        $user = UserSingleton::getUser();
        $variable = $user->variables()->where('variable', $variable)->first();
        abort_if(empty($variable), JsonResponse::HTTP_NOT_FOUND, 'Variable not found');

        return UserVariableResource::make($variable);
    }
}
