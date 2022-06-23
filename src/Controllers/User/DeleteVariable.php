<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\UserSingleton;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeleteVariable extends Controller
{
    /**
     * Invoke.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $variable
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request, $variable)
    {
        $user = UserSingleton::getUser();
        $variable = $user->variables()->where('variable', $variable)->first();
        abort_if(empty($variable), JsonResponse::HTTP_NOT_FOUND, 'Variable not found');

        $variable->delete();

        return response()->json([], JsonResponse::HTTP_ACCEPTED);
    }
}
