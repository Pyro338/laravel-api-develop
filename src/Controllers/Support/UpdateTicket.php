<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Support;

use Gamebetr\Api\Facades\Support;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UpdateTicket extends Controller
{
    /**
     * List support tickets.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $uuid
     *   The UUID of the ticket to update.
     *
     * @return JsonResponse
     *   The service response.
     */
    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        return response()->json(Support::updateTicket($uuid, $request->input()));
    }
}
