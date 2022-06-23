<?php

namespace Gamebetr\Api\Controllers\User;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Resources\UserCollection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

class ListUsers extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $userModel = GlobalAuth::userModel();
        $userModel::addGlobalScope(new RequestScope);
        if (! $request->has('sort')) {
            $request->request->add(['sort' => 'name']);
        }
        if ($request->has('affiliates')) {
            $users = $userModel::where('domain_id', Auth::user()->domain_id)->has('referrals')->get();
        } else {
            $users = $userModel::where('domain_id', Auth::user()->domain_id)->get();
        }
        //->limit($request->get('limit') ?? config('api.default_collection_limit', 50))
        //->offset($request->get('offset'))
        //->get();

        return new UserCollection($users);
    }
}
