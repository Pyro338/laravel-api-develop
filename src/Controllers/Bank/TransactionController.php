<?php

declare(strict_types=1);

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Facades\UserSingleton;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!Auth::user()->domain_admin) {
            $_GET['filter']['bank-account.player-id'] = Auth::id();
        }
        // FORCE PAGE SIZE
        if (!$request->has('page.size')) {
            $_GET['page']['size'] = 50;
            $_GET['page']['number'] = 1;
        }

        return response()->json(Bank::getTransactions($_GET));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function show(Request $request, string $uuid): JsonResponse
    {
        return response()->json(Bank::getTransaction($uuid, $request->all()) ?? []);
    }

    /**
     * Get transaction notes.
     *
     * @param Request $request
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function notes(Request $request, string $uuid): JsonResponse
    {
        $notes = Bank::getTransactionNotes($uuid, $request->all());
        return response()->json($notes['data'] ?? []);
    }
}
