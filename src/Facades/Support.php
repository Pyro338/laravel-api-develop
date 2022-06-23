<?php

declare(strict_types=1);

namespace Gamebetr\Api\Facades;

use Gamebetr\Api\Services\SupportService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array listTickets(array $filters = [])
 * @method static array getTicket(string $uuid, array $filters = [])
 * @method static array createTicket(string $title, string $body, array $params = [])
 * @method static array updateTicket(string $uuid, array $input)
 *
 * @see SupportService
 */
class Support extends Facade
{
    /**
     * Get facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'support-service';
    }
}
