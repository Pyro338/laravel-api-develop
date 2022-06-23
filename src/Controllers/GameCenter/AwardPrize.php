<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\Prize;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AwardPrize extends Controller
{

  /**
   * Invoke.
   *
   * @param Request $request
   *
   * @return mixed
   */
    public function __invoke(Request $request)
    {
      abort_if(!Auth::user()->domain_admin, JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Domain admin token required');
      return response()->json(Prize::awardPrize(
          $request->get('player_id'),
          $request->get('prize_key'),
          $request->get('value'),
          $request->get('optional_params'),
      ));
    }
}
