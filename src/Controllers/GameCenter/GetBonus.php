<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class GetBonus extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request, $uuid)
    {
        $filters = (array) $request->get('filter');
        if(!Auth::user()->domain_admin) {
            $filters['player_id'] = Auth::id();
        }
        $request->request->set('filter', $filters);

        return GameCenter::getBonus($uuid, $request->all());
    }
}
