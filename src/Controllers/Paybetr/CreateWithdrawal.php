<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Gamebetr\Api\Resources\PaybetrWithdrawalResource;
use Gamebetr\Api\Rules\CurrencyExists;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CreateWithdrawal extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'symbol' => new CurrencyExists,
            'address' => 'required',
            'amount' => 'required|numeric|gt:0',
        ]);
        $withdrawal = Paybetr::createWithdrawal($request->get('symbol'), $request->get('address'), $request->get('amount'), Auth::id());

        return PaybetrWithdrawalResource::make($withdrawal);
    }
}
