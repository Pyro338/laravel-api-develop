<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Tags as TagFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *   The incoming request.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(TagFacade::listTags($request->input()));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $uuid
     *   The model UUID.
     *
     * @return JsonResponse
     */
    public function show(Request $request, string $uuid): JsonResponse
    {
        return response()->json(TagFacade::getTag($uuid, $request->input()));
    }
}
