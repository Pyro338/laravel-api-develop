<?php

namespace Gamebetr\Api\Facades;

use Illuminate\Support\Facades\Facade;

class GameCenter extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'game-center-service';
    }
}
