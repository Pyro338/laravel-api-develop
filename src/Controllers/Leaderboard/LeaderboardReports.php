<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Leaderboard;

use Gamebetr\Api\Facades\Leaderboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LeaderboardReports extends Controller
{
    public function reportTopBet(Request $request): JsonResponse
    {
        return response()->json(Leaderboard::reportTopBet($request->input('filter', []), $request->input('page', [])));
    }

    public function reportMostBets(Request $request): JsonResponse
    {
        return response()->json(Leaderboard::reportMostBets($request->input('filter', []), $request->input('page', [])));
    }
}
