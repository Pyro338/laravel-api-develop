<?php

namespace Gamebetr\Api\Controllers\Web;

use Gamebetr\Api\Facades\Paybetr;
use Gamebetr\Api\Models\PaybetrWithdrawal;
use Gamebetr\Api\Services\TemplateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class CancelWithdrawal extends Controller
{
    public function __invoke(Request $request, $uuid, $token)
    {
        $withdrawal = PaybetrWithdrawal::uuid($uuid)->first();
        abort_if(empty($withdrawal), Response::HTTP_NOT_FOUND, 'Unknown withdrawal');

        $cachedWithdrawal = Cache::pull('withdrawal_'.$token);
        abort_if(empty($cachedWithdrawal), Response::HTTP_NOT_FOUND, 'Withdrawal token not found');

        abort_unless(
            $withdrawal->uuid === $cachedWithdrawal->uuid,
            Response::HTTP_NOT_FOUND,
            'Withdrawal does not match token'
        );

        Paybetr::cancelWithdrawal($withdrawal);
        $template = new TemplateService($request);
        $variables = $template->getVariables();

        return view('api::user.default.cancelwithdrawal', ['variables' => $variables]);
    }
}
