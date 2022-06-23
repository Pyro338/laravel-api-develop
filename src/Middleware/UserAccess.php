<?php

namespace Gamebetr\Api\Middleware;

use Closure;
use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Facades\UserSingleton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAccess
{
    /**
     * Validate requests.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $requestingUser = Auth::user();
            if (! $uuid = $request->get('user_uuid')) {
                $uuid = $requestingUser->uuid;
            }
            if ($uuid == $requestingUser->uuid) {
                UserSingleton::setUser($requestingUser);

                return $next($request);
            }
            if (! $requestingUser->domain_admin) {
                throw new Exception();
            }
            if (! $user = GlobalAuth::getUser($uuid)) {
                throw new Exception();
            }
            if ($requestingUser->domain_id != $user->domain_id) {
                throw new Exception();
            }
            UserSingleton::setUser($user);

            return $next($request);
        } catch (Exception $e) {
            throw new Exception('Invalid request', 403);
        }
    }
}
