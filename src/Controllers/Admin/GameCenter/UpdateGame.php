<?php

namespace Gamebetr\Api\Controllers\Admin\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UpdateGame extends Controller
{
    /**
     * Invoke.
     *
     * @param Request $request
     */
    public function __invoke(Request $request, $uuid)
    {
        return response()->json(GameCenter::updateGame($uuid, $request->all()));
    }
}
