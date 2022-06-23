<?php

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ListHolds extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if (!Auth::user()->domain_admin) {
            $_GET['filter']['bank-account.player-id'] = Auth::id();
        }
        // FORCE PAGE SIZE
        if(!$request->has('page.size')) {
            $_GET['page']['size'] = 50;
            $_GET['page']['number'] = 1;
        }

        return response()->json(Bank::getHolds($_GET));
    }
}
