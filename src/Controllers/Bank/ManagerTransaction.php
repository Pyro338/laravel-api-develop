<?php

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ManagerTransaction extends Controller
{
    /**
     * Invoke.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'player_id' => 'required',
            'type' => 'required',
            'amount' => 'required|numeric',
            'note' => 'nullable',
        ]);

        $account = Bank::getAccountByPlayerIdAndType($request->get('player_id'), $request->get('type'));
        abort_if(empty($account), JsonResponse::HTTP_NOT_FOUND, 'Unknown account');

        return response()->json(
            Bank::createTransaction(
                $account['id'],
                $request->get('amount'),
                'manager',
                'back-office',
                [
                    'note' => $request->get('note'),
                ]
            )
        );
    }
}
