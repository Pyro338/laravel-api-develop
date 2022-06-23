<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Services\AffiliateService;
use Gamebetr\Api\Resources\AffiliateMediaGroupResource;

class CreateMediaGroup extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $request->validate([
                'domain_id' => 'required|integer',
                'title' => 'required',
                'landing_page_url' => 'required',
            ]);
            $affiliate = new AffiliateService();
            $result = $affiliate->saveMediaGroup($request->all());
            return new AffiliateMediaGroupResource($result);
        }
    }
}
