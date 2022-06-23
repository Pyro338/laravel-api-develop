<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Resources\AffiliateClickResource;
use Gamebetr\Api\Models\AffiliateClick;
use Illuminate\Support\Facades\Auth;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class ListClicks extends Controller
{
    public function __invoke(Request $request)
    {
        $filter = $request->input('filter') ?? [];

        if(!Auth::user()->domain_admin) {
            $filter['player_id'] = (int)Auth::id();
            $filter['domain_id'] = (int) Auth::user()->domain_id;
        }

        $request->request->set('filter', $filter);

        AffiliateClick::addGlobalScope(new RequestScope);        
        return AffiliateClickResource::collection(AffiliateClick::get());
    }
}
