<?php

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListBanks extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        $banks = Bank::getBanks(collect($request->input())->only(['filter', 'sort', 'include', 'page'])->toArray());

        return response()->json($banks);
    }
}
