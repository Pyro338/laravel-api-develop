<?php

namespace Gamebetr\Api\Facades;

use Illuminate\Support\Facades\Facade;

class Vip extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vip-service';
    }
}
