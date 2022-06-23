<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListRates extends Controller
{
    public function __invoke(Request $request)
    {
        return 'return rates model';
    }
}
