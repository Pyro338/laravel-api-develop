<?php

namespace Gamebetr\Api\Controllers\User;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Models\User;
use Gamebetr\Api\Resources\UserResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Submtd\LaravelRequestScope\Scopes\RequestScope;

/**
 * Return user information based on a UUID.
 */
class Find extends Controller
{
    /**
     * Return user information based on a UUID.
     *
     * Returns 400 if no uuid is present.
     * Returns 403 if the requesting user is not an admin.
     * Returns 404 if no user is found.
     *
     * @param Request $request
     *   The incoming request.
     * @param string $uuid
     *   The user id being requested.
     *
     * @return UserResource
     *   The user information if it was a valid user.
     */
    public function __invoke(Request $request, string $uuid)
    {
        abort_if(empty($uuid), JsonResponse::HTTP_BAD_REQUEST);

        $requestingUser = Auth::user();
        abort_unless($requestingUser->domain_admin ?? false, JsonResponse::HTTP_FORBIDDEN);

        /** @var Model|User $userModel */
        $userModel = GlobalAuth::userModel();
        $userModel::addGlobalScope(new RequestScope);

        /** @var Model|User $result */
        $result = $userModel::whereUuid($uuid)->first();
        abort_if($result === null, JsonResponse::HTTP_NOT_FOUND);

        return UserResource::make($result);
    }
}
