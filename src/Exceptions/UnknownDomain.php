<?php

namespace Gamebetr\Api\Exceptions;

use Exception;
use Throwable;

class UnknownDomain extends Exception
{
    /**
     * Class constructor.
     */
    public function __construct($message = 'Unkown domain', $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
