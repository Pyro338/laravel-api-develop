<?php

namespace Gamebetr\Api\Controllers\Admin\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CreateBonus extends Controller
{
    /**
     * Invoke.
     *
     * @param Request $request
     */
    public function __invoke(Request $request)
    {
        return response()->json(GameCenter::createBonus(
            $request->get('player_id'),
            $request->get('currency'),
            (array) $request->get('games'),
            $request->get('balance'),
            $request->get('target_balance'),
            $request->get('expires_at')
        ));
    }
}
