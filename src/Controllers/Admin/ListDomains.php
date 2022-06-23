<?php

namespace Gamebetr\Api\Controllers\Admin;

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
     */
    public function __invoke(Request $request)
    {
        Domain::addGlobalScope(new RequestScope);
        return DomainResource::collection(Domain::get());
    }
}
