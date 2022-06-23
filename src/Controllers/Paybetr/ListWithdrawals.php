<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Models\PaybetrWithdrawal;
use Gamebetr\Api\Resources\PaybetrWithdrawalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class ListWithdrawals extends Controller
{
    /**
     * Invoke.
     *
     * @param string $symbol
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        PaybetrWithdrawal::addGlobalScope(new RequestScope);
        $filter = $request->input('filter') ?? [];
        $filter['domain_id'] = GlobalAuth::getDomain()->id;
        if (!Auth::user()->domain_admin) {
            $filter['player_id'] = (int)Auth::id();
        }
        $request->request->set('filter', $filter);
        $page = $request->input('page') ?? [];
        if (!$request->has('page.size')) {
            $page['size'] = 50;
        }
        if (!$request->has('page.number')) {
            $page['number'] = 0;
        }
        $request->request->set('page', $page);
        if (!$request->has('sort')) {
            $request->request->set('sort', '-created_at');
        }
        try {
            $withdrawals = PaybetrWithdrawal::get();
            return PaybetrWithdrawalResource::collection($withdrawals);
        }
        catch (Exception $e) {
            abort(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
