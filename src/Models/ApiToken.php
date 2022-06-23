<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Models\ApiToken as GlobalAuthApiToken;

class ApiToken extends GlobalAuthApiToken
{
    /**
     * Belongs to domain.
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
