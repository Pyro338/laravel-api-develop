<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Services\AffiliateService;
use Gamebetr\Api\Resources\AffiliateMediaItemResource;

class CreateMediaItem extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $request->validate([
                'domain_id' => 'required|integer',
                'media_group_id' => 'required|integer',
                'title' => 'required',
                'filepath' => 'required',
            ]);
            $affiliate = new AffiliateService();
            $result = $affiliate->saveMediaItem($request->all());
            return new AffiliateMediaItemResource($result);
        }
    }
}
