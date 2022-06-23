<?php

namespace Gamebetr\Api\Middleware;

use Closure;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\SignedRequest\Signer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GameCenterCallback
{
    /**
     * Handle.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Closure
     */
    public function handle(Request $request, Closure $next)
    {
        if (env('TEST_GAMECENTER_CALLBACKS', false)) {
            return $next($request);
        }
        $domain = GlobalAuth::getDomain();
        $encryptionKey = $domain->variable('game_center_encryption_key');
        if (!Signer::init($encryptionKey)->validate($request->all())) {
            Log::debug(json_encode([
                'error' => 'failed validation for gamecenter callback',
                'domain' => $domain,
                'data' => $request->all(),
            ]));
            throw new Exception('Invalid request', 403);
        }
        return $next($request);
    }
}
