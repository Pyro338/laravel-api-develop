<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetTicket extends Controller
{
    /**
     * Invoke.
     * @param string $ticketHash
     */
    public function __invoke(Request $request, $ticketHash)
    {
        return response()->json(GameCenter::getTicket($ticketHash, $request->all()));
    }
}
