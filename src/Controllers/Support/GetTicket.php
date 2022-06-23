<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Support;

use Gamebetr\Api\Facades\Support;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetTicket extends Controller
{
    /**
     * Get a support ticket.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $uuid
     *   The UUID to retrieve.
     *
     * @return JsonResponse
     *   The service response.
     */
    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        return response()->json(Support::getTicket($uuid, $request->input()));
    }
}

