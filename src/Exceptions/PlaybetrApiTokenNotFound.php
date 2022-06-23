<?php

namespace Gamebetr\Api\Exceptions;

use Exception;
use Throwable;

class PlaybetrApiTokenNotFound extends Exception
{
    /**
     * Class constructor.
     */
    public function __construct($message = 'Playbetr API token not found', $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
