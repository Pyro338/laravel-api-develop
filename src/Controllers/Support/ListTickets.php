<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Support;

use Gamebetr\Api\Facades\Support;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListTickets extends Controller
{
    /**
     * List support tickets.
     *
     * @param Request $request
     *   The incoming request.
     *
     * @return JsonResponse
     *   The service response.
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(Support::listTickets($request->input()));
    }
}

