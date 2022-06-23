<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Routing\Controller;

class ListBalances extends Controller
{
    /**
     * Invoke.
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return response()->json(Paybetr::listBalances());
    }
}
