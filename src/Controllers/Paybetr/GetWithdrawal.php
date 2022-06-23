<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Models\PaybetrWithdrawal;
use Gamebetr\Api\Resources\PaybetrWithdrawalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class GetWithdrawal extends Controller
{
    /**
     * Invoke.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $uuid)
    {
        PaybetrWithdrawal::addGlobalScope(new RequestScope);

        $withdrawal = PaybetrWithdrawal::uuid($uuid)->first();
        abort_if(empty($withdrawal), JsonResponse::HTTP_NOT_FOUND, 'Unknown withdrawal');

        $user = Auth::user();
        $is_domain_admin = !is_null($user) && !empty($user->domain_admin);

        abort_if(
            !$is_domain_admin && $withdrawal->player_id != $user->id,
            JsonResponse::HTTP_FORBIDDEN,
            'Unauthorized'
        );

        return PaybetrWithdrawalResource::make($withdrawal);
    }
}
