<?php

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetCurrency extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $symbol)
    {
        return response()->json(Bank::getCurrencyBySymbol($symbol));
    }
}
