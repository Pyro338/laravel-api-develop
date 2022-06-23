<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConvertCurrency extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $symbol, $to, $amount = 1)
    {
        return response()->json(['data' => Paybetr::convertCurrency($symbol, $to, $amount)]);
    }
}
