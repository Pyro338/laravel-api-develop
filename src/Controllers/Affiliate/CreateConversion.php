<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use Gamebetr\Api\Facades\Affiliate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Resources\AffiliateConversionResource;

class CreateConversion extends Controller
{
    public function __invoke(Request $request)
    {
        return new AffiliateConversionResource(Affiliate::saveConversion($request->all()));
    }
}
