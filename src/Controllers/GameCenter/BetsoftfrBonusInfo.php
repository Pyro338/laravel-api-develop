<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class BetsoftfrBonusInfo extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke(Request $request)
    {
        $query = $request->query();
        if (!Auth::user()->domain_admin) {
            $query['player_id'] = (int)Auth::id();
        }
        $request = GameCenter::betsoftfrBonusInfo($query);
        $request = json_decode($request, true);
        $data = [];
        if (isset($request['data'])) {
            foreach ($request['data'] as $item) {
                $data[] = [
                    'type' => 'frBonusInfo',
                    'id' => $item['bonus_id'],
                    'attributes' => $item,
                ];
            }
        }
        return response()->json($data);
    }
}
