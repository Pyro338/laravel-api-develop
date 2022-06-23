<?php

namespace Gamebetr\Api\Exceptions;

use Exception;
use Throwable;

class ValidationError extends Exception
{
    /**
     * Class constructor.
     */
    public function __construct($message = 'Validation error', $code = 422, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
