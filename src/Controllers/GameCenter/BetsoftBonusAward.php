<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BetsoftBonusAward extends Controller
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
      return response()->json(GameCenter::betsoftBonusAward(
          $request->get('domain_id'),
          $request->get('player_id'),
          $request->get('bank_id'),
          $request->get('currency'),
          $request->get('type'),
          $request->get('amount'),
          $request->get('multiplier'),
          $request->get('games'),
          $request->get('game_ids'),
          $request->get('exp_date'),
          $request->get('comment'),
          $request->get('description'),
      ));
    }
}
