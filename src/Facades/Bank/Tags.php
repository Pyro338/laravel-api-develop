<?php

declare(strict_types=1);

namespace Gamebetr\Api\Facades\Bank;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|string listTags(array $query = [])
 * @method static array|string getTag(string $uuid, array $query = [])
 *
 * @see \Gamebetr\Api\Services\Bank\Tags
 */
class Tags extends Facade
{
    /**
     * Get facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bank-tags';
    }
}
