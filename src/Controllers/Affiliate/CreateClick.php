<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use Gamebetr\Api\Facades\Affiliate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Resources\AffiliateClickResource;

class CreateClick extends Controller
{
    public function __invoke(Request $request)
    {
        return new AffiliateClickResource(Affiliate::saveClick($request->all()));
    }
}
