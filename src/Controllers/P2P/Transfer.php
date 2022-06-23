<?php

namespace Gamebetr\Api\Controllers\P2P;

use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Facades\UserSingleton;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Transfer extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'to_player_id' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required',
        ]);
        $user = UserSingleton::getUser();

        return response()->json(Bank::p2pTransfer($user, (int)$request->get('to_player_id'), $request->get('type'), $request->get('amount'), $request->get('notes')));
    }
}
