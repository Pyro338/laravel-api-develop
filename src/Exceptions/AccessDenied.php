<?php

namespace Gamebetr\Api\Exceptions;

use Exception;
use Throwable;

class AccessDenied extends Exception
{
    /**
     * Class constructor.
     */
    public function __construct($message = 'Access denied', $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
