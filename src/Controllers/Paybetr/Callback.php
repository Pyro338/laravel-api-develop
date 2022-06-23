<?php

namespace Gamebetr\Api\Controllers\Paybetr;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\Paybetr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Callback extends Controller
{
    /**
     * Invoke.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $is_authed = $request->get('token') == GlobalAuth::getDomain()->variable('paybetr_callback_token');
        abort_unless($is_authed, JsonResponse::HTTP_UNAUTHORIZED, 'Unauthenticated');

        Paybetr::processCallback($request->all());
        return 'OK';
    }
}
