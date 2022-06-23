<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ListAddresses extends Controller
{
    /**
     * Invoke.
     * @param string $symbol
     * @return \Illuminate\Http\Response
     */
    public function __invoke($symbol = null)
    {
        return response()->json(Paybetr::listAddresses(Auth::id(), $symbol));
    }
}
