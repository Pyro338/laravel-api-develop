<?php

namespace Gamebetr\Api\Controllers\Vip;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListConfig extends Controller
{
    public function __invoke(Request $request)
    {
        return config('vip');
    }
}
