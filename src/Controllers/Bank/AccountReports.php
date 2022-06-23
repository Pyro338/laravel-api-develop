<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Facades\UserSingleton;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;

/**
 * Bank Account report related endpoints.
 */
class AccountReports extends Controller
{
    /**
     * Perform an account Win/Loss report for a given date range.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $currencyType
     *   The currency type to perform an account lookup for.
     * @param mixed|null $startDate
     *   An optional start date for the win/loss results.
     * @param mixed|null $endDate
     *   An optional end date for the win/loss results.
     *
     * @return JsonResponse
     *   The API response.
     */
    public function winLoss(Request $request, string $currencyType, $startDate = null, $endDate = null): JsonResponse {
        $user = UserSingleton::getUser();
        $account = Bank::getAccountByCurrency($user->id, $currencyType);
        abort_if(empty($account), JsonResponse::HTTP_NOT_FOUND, 'Unknown account');

        $args = $request->input();
        if (!is_null($startDate)) {
            $args['filter']['date-start'] = Carbon::parse($startDate)->format('Y-m-d');
        }
        if (!is_null($endDate)) {
            $args['filter']['date-end'] = Carbon::parse($endDate)->format('Y-m-d');
        }

        return response()->json(Bank::getAccountWinLoss($account['data']['id'], $args));
    }

    /**
     * Perform an account Win/Loss report grouped by the given tag(s).
     *
     * @param Request $request
     *   The incoming request.
     * @param string $currencyType
     *   The currency type to perform an account lookup for.
     *
     * @return JsonResponse
     *   The API response.
     */
    public function winLossByTags(Request $request, string $currencyType): JsonResponse
    {
        $user = UserSingleton::getUser();
        $account = Bank::getAccountByCurrency($user->id, $currencyType);
        abort_if(empty($account), JsonResponse::HTTP_NOT_FOUND, 'Unknown account');

        return response()->json(Bank::reportAccountWinLossByTags($account['data']['id'], $request->input()));
    }
}
