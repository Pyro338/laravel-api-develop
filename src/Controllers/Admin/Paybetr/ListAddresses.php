<?php

namespace Gamebetr\Api\Controllers\Admin\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListAddresses extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json(Paybetr::listAllAddresses($request->all()));
    }
}
