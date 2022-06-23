<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Services\AffiliateService;

class CreateUser extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $affiliate = new AffiliateService;
            return $affiliate->saveUser($request->all());
        } catch (Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }
}
