<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use Gamebetr\Api\Models\PaybetrWithdrawal;
use Gamebetr\Api\Models\Withdrawal;
use Gamebetr\Api\Resources\WithdrawalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class CancelWithdrawal extends Controller
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
        $withdrawal = PaybetrWithdrawal::uuid($uuid)->first();
        return $withdrawal;
        Withdrawal::addGlobalScope(new RequestScope);

        $withdrawal = Withdrawal::uuid($uuid)->first();
        abort_if(empty($withdrawal), JsonResponse::HTTP_NOT_FOUND, 'Unknown withdrawal');

        $user = Auth::user();
        $is_same_id = !is_null($user) && $withdrawal->user_id === $user->id;
        abort_unless($is_same_id, JsonResponse::HTTP_NOT_FOUND, 'Unknown withdrawal');

        $invalid_actions = ['confirmed', 'approved', 'sent', 'cancelled', 'refunded'];
        foreach ($invalid_actions as $invalid_action) {
            abort_unless(
                empty($withdrawal->{$invalid_action}),
                JsonResponse::HTTP_BAD_REQUEST,
                'Invalid withdrawal action'
            );
        }

        $withdrawal->update([
            'cancelled' => true,
        ]);

        return WithdrawalResource::make($withdrawal);
    }
}
