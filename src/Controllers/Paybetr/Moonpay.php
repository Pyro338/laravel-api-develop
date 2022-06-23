<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Facades\Paybetr;
use Gamebetr\Api\Resources\PaybetrWithdrawalResource;
use Gamebetr\Api\Rules\CurrencyExists;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Moonpay extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'currency' => 'required',
        ]);
        $user = Auth::user();

        return response()->json([
            'type' => 'moonpay',
            'id' => Str::uuid()->toString(),
            'attributes' => Paybetr::moonpay($user, $request->input('currency')),
        ]);
    }
}
