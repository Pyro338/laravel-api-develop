<?php

namespace Gamebetr\Api\Controllers\Admin\GlobalAuth;

use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Resources\DomainResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class ListDomains extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        Domain::addGlobalScope(new RequestScope);
        if (!$request->get('limit')) {
            $request->request->add(['limit' => 50]);
        }
        if (!$request->get('offset')) {
            $request->request->add(['offset' => 0]);
        }

        return DomainResource::collection(Domain::get());
    }
}
