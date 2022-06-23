<?php

namespace Gamebetr\Api\Services\Prize;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;

class FreeSpinsService
{
    /**
     * Award.
     *
     * @param [] $params
     */
    public function Award($params)
    {
        return response()->json(GameCenter::betsoftfrBonusAward(
            $params['optional_params']['domain_id'],
            $params['player_id'],
            $params['optional_params']['bank_id'],
            $params['optional_params']['currency'],
            $params['value'], // rounds
            $params['optional_params']['game_ids'],
            $params['optional_params']['comment'],
            $params['optional_params']['description'],
            $params['optional_params']['start_time'],
            $params['optional_params']['exp_time'],
            $params['optional_params']['duration'],
            $params['optional_params']['exp_hours'],
            $params['optional_params']['table_round_chips'],
        ));
    }
}
