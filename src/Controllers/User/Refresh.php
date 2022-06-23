<?php

namespace Gamebetr\Api\Controllers\User;

use Gamebetr\Api\Facades\Api;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Refresh extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json(Api::refresh($request->get('refresh_token')));
    }
}
