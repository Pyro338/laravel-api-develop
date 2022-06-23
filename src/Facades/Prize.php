<?php

namespace Gamebetr\Api\Facades;

use Gamebetr\Api\Services\PrizeService;
use Illuminate\Support\Facades\Facade;

/**
 *
 * @see PrizeService
 */
class Prize extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'prize-service';
    }
}
