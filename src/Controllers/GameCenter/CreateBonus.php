<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class CreateBonus extends Controller
{

  /**
   * Invoke.
   *
   * @param \Illuminate\Http\Request $request
   *
   * @return mixed
   */
    public function __invoke(Request $request)
    {
      abort_if(!Auth::user()->domain_admin, JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Domain admin token required');
      return response()->json(GameCenter::createBonus(
          $request->get('domain_id'),
          $request->get('player_id'),
          $request->get('user_id'),
          $request->get('external_id'),
          $request->get('balance'),
          $request->get('target_balance'),
          $request->get('games'),
      ));
    }
}
