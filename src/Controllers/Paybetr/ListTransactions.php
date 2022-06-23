<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Models\PaybetrTransaction;
use Gamebetr\Api\Resources\PaybetrTransactionResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class ListTransactions extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        PaybetrTransaction::addGlobalScope(new RequestScope);
        $filter = $request->get('filter');
        $filter['domain_id'] = GlobalAuth::getDomain()->id;
        if(!Auth::user()->domain_admin) {
            $filter['player_id'] = Auth::id();
        }
        $request->request->set('filter', $filter);

        return PaybetrTransactionResource::collection(PaybetrTransaction::get());
    }
}
