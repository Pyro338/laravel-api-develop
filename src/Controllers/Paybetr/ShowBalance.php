<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Routing\Controller;

class ShowBalance extends Controller
{
    /**
     * Invoke.
     * @param string $symbol
     * @return \Illuminate\Http\Response
     */
    public function __invoke($symbol)
    {
        return response()->json(Paybetr::showBalance($symbol));
    }
}
