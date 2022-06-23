<?php

namespace Gamebetr\Api\Facades\Prize;

use Gamebetr\Api\Services\Prize\StatusPointsService;
use Illuminate\Support\Facades\Facade;

/**
 *
 * @see StatusPointsService
 */
class StatusPoints extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'status-points-service';
    }
}
