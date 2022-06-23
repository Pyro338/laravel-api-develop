<?php

namespace Gamebetr\Api\Exceptions;

use Exception;
use Throwable;

class PaybetrApiTokenNotFound extends Exception
{
    /**
     * Class constructor.
     */
    public function __construct($message = 'Paybetr API token not found', $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
