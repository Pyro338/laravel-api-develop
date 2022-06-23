<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ListTickets extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request)
    {
        if(!Auth::user()->domain_admin) {
            $filter = $request->input('filter') ?? [];
            $filter['player_id'] = (int)Auth::id();
            $request->request->set('filter', $filter);
        }
        return GameCenter::listTickets($request->all());
    }
}
