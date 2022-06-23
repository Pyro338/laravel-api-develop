<?php

namespace Gamebetr\Api\Facades\Prize;

use Gamebetr\Api\Services\Prize\FreeSpinsService;
use Illuminate\Support\Facades\Facade;

/**
 *
 * @see FreeSpinsService
 */
class FreeSpins extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'free-spins-service';
    }
}
