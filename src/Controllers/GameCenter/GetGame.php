<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetGame extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request, $uuid)
    {
        return GameCenter::getGame($uuid, $request->all());
    }
}
