<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListCurrencies extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json(['data' => Paybetr::listCurrencies($_GET)]);
    }
}
