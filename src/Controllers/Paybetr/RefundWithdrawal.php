<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Facades\Paybetr;
use Gamebetr\Api\Models\PaybetrWithdrawal;
use Gamebetr\Api\Resources\PaybetrWithdrawalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RefundWithdrawal extends Controller
{
    /**
     * Invoke.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request, $uuid)
    {
        $user = Auth::user();
        $is_domain_admin = !is_null($user) && !empty($user->domain_admin);

        abort_unless($is_domain_admin, JsonResponse::HTTP_FORBIDDEN, 'Unauthorized');
        $withdrawal = PaybetrWithdrawal::uuid($uuid)->first();
        abort_if(empty($withdrawal), JsonResponse::HTTP_NOT_FOUND, 'Unknown withdrawal');
        $is_same_id = $withdrawal->user_id === $user->id;
        abort_unless($is_same_id, JsonResponse::HTTP_NOT_FOUND, 'Unknown withdrawal');

        return PaybetrWithdrawalResource::make(Paybetr::refundWithdrawal($withdrawal));
    }
}
