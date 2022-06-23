<?php

namespace Gamebetr\Api\Services\Prize;

use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Http\Request;

class StatusPointsService
{
    /**
     * Award.
     *
     * @param [] $params
     */
    public function Award($params)
    {
        if(!$statusPointsAccount = Bank::getAccountByPlayerIdAndType($params['player_id'], 'status_points')) {
            $this->info('Invalid status points account for player : ' . json_encode($params['player_id']));
            abort(404, 'Invalid status points account');
        }
        $requiredOptionals = [
            'transaction-type',
            'service',
        ];

        $filteredOptionals = array_filter($params['optional_params'], function($el) use ($requiredOptionals) {
            return !in_array($el, $requiredOptionals);
        }, ARRAY_FILTER_USE_KEY);

        return response()->json(Bank::createTransaction(
            $statusPointsAccount['id'],
            $params['value'],
            $params['optional_params']['transaction-type'],
            $params['optional_params']['service'],
            $filteredOptionals,
        ));
    }
}
