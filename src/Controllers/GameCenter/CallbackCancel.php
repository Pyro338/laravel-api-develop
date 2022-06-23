<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CallbackCancel extends Controller
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
        return GameCenter::processCancelCallback($request);
    }
}
