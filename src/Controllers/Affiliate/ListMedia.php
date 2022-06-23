<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Resources\AffiliateMediaItemResource;
use Gamebetr\Api\Models\AffiliateMediaItem;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class ListMedia extends Controller
{
    public function __invoke(Request $request)
    {
        AffiliateMediaItem::addGlobalScope(new RequestScope);
        $result = AffiliateMediaItem::limit($request->get('limit') ?? 50)
            ->offset($request->get('offset'))
            ->get();
        return AffiliateMediaItemResource::collection($result);
    }
}
