<?php

namespace Gamebetr\Api\Controllers\GameCenter;

use Gamebetr\Api\Facades\GameCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LaunchGame extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request, $uuid)
    {
        if (!Auth::id()) {
            $request->request->add(['anonymous' => 1]);
        }
        $request->validate([
            'anonymous' => 'nullable|boolean',
            'currency' => 'nullable',
            'private' => 'nullable|boolean',
            'language' => 'nullable',
            'country' => 'nullable',
            'parameters' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        return response()->json(GameCenter::launchGame(
            $uuid,
            filter_var($request->get('anonymous'), FILTER_VALIDATE_BOOL),
            $request->get('currency'),
            filter_var($request->get('private'), FILTER_VALIDATE_BOOL),
            $request->get('language'),
            $request->get('country'),
            (array) $request->get('parameters'),
            (array) $request->get('tags')
        ));
    }
}
