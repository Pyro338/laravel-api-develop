<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Gamebetr\Api\Models\PaybetrWithdrawal;
use Gamebetr\Api\Resources\PaybetrWithdrawalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ApproveWithdrawal extends Controller
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
        $user = Auth::user();
        abort_if(empty($user->domain_admin), JsonResponse::HTTP_FORBIDDEN, 'Unauthorized');

        $withdrawal = PaybetrWithdrawal::uuid($uuid)->first();
        abort_if(empty($withdrawal), JsonResponse::HTTP_NOT_FOUND, 'Unknown withdrawal');

        return PaybetrWithdrawalResource::make(Paybetr::approveWithdrawal($withdrawal));
    }
}