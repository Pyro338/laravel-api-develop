<?php

declare(strict_types=1);

namespace Gamebetr\Api\Facades;

use Gamebetr\Api\Services\LeaderboardService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array reportMostBets(array $filters = [], array $page = [])
 * @method static array reportTopBet(array $filters = [], array $page = [])
 *
 * @see LeaderboardService
 */
class Leaderboard extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return 'leaderboard';
    }
}
