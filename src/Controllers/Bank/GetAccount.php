<?php

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class GetAccount extends Controller
{
    /**
     * Handle incoming requests for Bank Accounts by their type.
     *
     * @param  Request  $request
     * @param  string  $type
     * @return JsonResponse
     */
    public function getByType(Request $request, string $type): JsonResponse
    {
        return response()->json(Bank::getAccountByPlayerIdAndType(Auth::id(), $type));
    }

    /**
     * Handle incoming requests for Bank Accounts by their UUID.
     *
     * @param  Request  $request
     * @param  string  $uuid
     * @return JsonResponse
     */
    public function getByUuid(Request $request, string $uuid): JsonResponse
    {
        return response()->json(Bank::getAccount($uuid, $request->input()));
    }
}
