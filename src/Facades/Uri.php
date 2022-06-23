<?php

namespace Gamebetr\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string baseUri(string $service)
 * @method static string|null token()
 * @see \Gamebetr\Api\Services\UriService
 */
class Uri extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'uri-service';
    }
}
