<?php

namespace Gamebetr\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gamebetr\Api\Services\TransactionBatchService
 */
class TransactionBatch extends Facade
{
    /**
     * Get facade accessor.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'transaction-batch-service';
    }
}
