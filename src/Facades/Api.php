<?php

namespace Gamebetr\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Contracts\Auth\Authenticatable login(string $email, string $password, string $key)
 * @method static \Illuminate\Contracts\Auth\Authenticatable register(int $domainId, string $name, string $email, string $password)
 * @see \Gamebetr\Api\Services\ApiService
 */
class Api extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'api-service';
    }
}
