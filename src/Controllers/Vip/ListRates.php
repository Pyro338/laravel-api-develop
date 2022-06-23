<?php

namespace Gamebetr\Api\Controllers\Vip;

use Gamebetr\Api\Facades\Vip;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListRates extends Controller
{
    public function __invoke(Request $request)
    {
        return Vip::listRates($request->getQueryString());
    }
}
