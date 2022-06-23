<?php

namespace Gamebetr\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gamebetr\Api\Services\PaybetrService
 */
class Paybetr extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'paybetr-service';
    }
}
