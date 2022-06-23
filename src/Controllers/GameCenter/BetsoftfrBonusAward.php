<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BetsoftfrBonusAward extends Controller
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
      return response()->json(GameCenter::betsoftfrBonusAward(
          $request->get('domain_id'),
          $request->get('player_id'),
          $request->get('bank_id'),
          $request->get('currency'),
          $request->get('rounds'),
          $request->get('game_ids'),
          $request->get('comment'),
          $request->get('description'),
          $request->get('start_time'),
          $request->get('exp_time'),
          $request->get('duration'),
          $request->get('exp_hours'),
          $request->get('table_round_chips')
      ));
    }
}
