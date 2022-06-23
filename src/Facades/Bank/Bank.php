<?php

namespace Gamebetr\Api\Facades\Bank;

use Gamebetr\Api\Services\BankService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getAccountByCurrency(int $playerId, string $currency)
 * @method static array getAccountWinLoss($accountUuid, array $args = [])
 * @method static array reportAccountWinLossByTags(string $accountUuid, array $args = [])
 * @method static array reportTopUsersByTag(string $bankUuid, array $args = [])
 * @method static array reportWinLoss(string $bankUuid, array $args = [])
 * @method static array reportWinLossByTags(string $bankUuid, array $args = [])
 * @method static array reportWinLossByTagsAggregate(string $bankUuid, array $args = [])
 * @method static array getBanks(array $query = [])
 * @method static array createTransaction(string $accountUuid, float $amount, string $type, string $service, array $optional = [])
 * @method static array getAccountByPlayerIdAndType(int $playerId, string $type)
 * @method static array getAccount(string $uuid, array $query = [])
 *
 * @see BankService
 */
class Bank extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bank-service';
    }
}
