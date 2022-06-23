<?php

namespace Gamebetr\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gamebetr\Api\Services\AffiliateProcessService
 */
class AffiliateProcess extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'affiliate-process-service';
    }
}
