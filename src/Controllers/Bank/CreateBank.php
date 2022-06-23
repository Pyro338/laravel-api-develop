<?php

namespace Gamebetr\Api\Controllers\Bank;

use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CreateBank extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'nullable',
            'description' => 'nullable',
            'hidden' => 'nullable|boolean',
            'transferrable' => 'nullable|boolean',
            'currency_uuid' => 'required|uuid',
            'display_currency_uuid' => 'required|uuid',
            'tags' => 'nullable|array',
        ]);
        return response()->json(Bank::createBank(
            $request->get('name'),
            $request->get('description'),
            filter_var($request->get('hidden'), FILTER_VALIDATE_BOOL),
            filter_var($request->get('transferrable'), FILTER_VALIDATE_BOOL),
            $request->get('currency_uuid'),
            $request->get('display_currency_uuid'),
            (array) $request->get('tags')
        ));
    }
}
