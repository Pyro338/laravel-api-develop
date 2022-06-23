<?php

namespace Gamebetr\Api\Controllers\Web;

use Gamebetr\Api\Facades\Avatar as AvatarFacade;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Avatar extends Controller
{
    public function __invoke(Request $request, string $avatar)
    {
        return response()->file(AvatarFacade::get($avatar));
    }
}
