<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ListBonuses extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request)
    {
        $filters = (array) $request->get('filter');
        if(!Auth::user()->domain_admin) {
            $filters['player_id'] = Auth::id();
        }
        $request->request->set('filter', $filters);

        return GameCenter::listBonuses($request->all());
    }
}
