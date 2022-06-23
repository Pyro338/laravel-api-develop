<?php

namespace Gamebetr\Api\Controllers\Bank;

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
            'from' => 'required',
            'to' => 'required',
            'amount' => 'required|numeric',
        ]);
        $user = UserSingleton::getUser();

        return response()->json(Bank::transfer($user, $request->get('from'), $request->get('to'), $request->get('amount')));
    }
}
