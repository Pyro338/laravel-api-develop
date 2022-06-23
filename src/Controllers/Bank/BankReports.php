<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank as BankFacade;
use Gamebetr\Api\Http\Requests\Bank\WinLossRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Bank report related endpoints.
 */
class BankReports extends Controller
{

    /**
     * General Win/Loss report.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $bankUuid
     *   The bank the report was requested for.
     *
     * @return JsonResponse
     *   The API response.
     */
    public function winLoss(WinLossRequest $request, string $bankUuid): JsonResponse
    {
        return response()->json(BankFacade::reportWinLoss($bankUuid, $request->input()));
    }

    /**
     * A Top users Win/Loss report grouped by tag.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $bankUuid
     *   The bank the report was requested for.
     *
     * @return JsonResponse
     *   The API response.
     */
    public function topUsersByTag(Request $request, string $bankUuid): JsonResponse
    {
        return response()->json(BankFacade::reportTopUsersByTag($bankUuid, $request->input()));
    }

    /**
     * Win/Loss report grouped by tags.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $bankUuid
     *   The bank the report was requested for.
     *
     * @return JsonResponse
     *   The API response.
     */
    public function winLossByTags(WinLossRequest $request, string $bankUuid): JsonResponse
    {
        return response()->json(BankFacade::reportWinLossByTags($bankUuid, $request->input()));
    }

    /**
     * Win/Loss report grouped by tags and predefined date periods.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $bankUuid
     *   The bank the report was requested for.
     *
     * @return JsonResponse
     *   The API response.
     */
    public function winLossByTagsAggregate(Request $request, string $bankUuid): JsonResponse
    {
        return response()->json(BankFacade::reportWinLossByTagsAggregate($bankUuid, $request->input()));
    }
}
