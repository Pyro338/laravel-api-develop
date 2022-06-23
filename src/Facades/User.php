<?php

namespace Gamebetr\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gamebetr\Api\Services\UserService
 */
class User extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'user-service';
    }
}
