<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CreateAddress extends Controller
{
    /**
     * Invoke.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'symbol' => 'required',
            'refresh' => 'nullable|boolean',
        ]);
        $symbol = strtoupper($request->get('symbol'));
        $account = Bank::getAccountByPlayerIdAndType(Auth::id(), $symbol);
        $currency = $account['attributes']['bank']['deposit-currency']['display-unit'] ?? null;
        abort_if(empty($currency), JsonResponse::HTTP_UNPROCESSABLE_ENTITY, 'This account cannot be used for deposits');

        return response()->json(
            ['data' => Paybetr::createAddress($currency, $account['id'], (bool)$request->get('refresh'))]
        );
    }
}
