<?php

namespace Gamebetr\Api\Exceptions;

use Exception;
use Throwable;

class UnknownServiceUrl extends Exception
{
    /**
     * Class constructor.
     */
    public function __construct($message = 'Unkown service url', $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
