<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Support;

use Gamebetr\Api\Facades\Support;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CreateTicket extends Controller
{
    /**
     * Create a support ticket.
     *
     * @param Request $request
     *   The incoming request.
     *
     * @return JsonResponse
     *   The service response.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $params = $request->input();

        return response()->json(Support::createTicket($params['title'] ?? null, $params['body'] ?? null, $params));
    }
}
