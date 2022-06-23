<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Models\Domain as GlobalAuthDomain;

class Domain extends GlobalAuthDomain
{
    /**
     * Has one api token.
     */
    public function apiToken()
    {
        return $this->hasOne(ApiToken::class);
    }

    /**
     * Drupal mappings relationship.
     */
    public function drupalMapings()
    {
        return $this->hasMany(DrupalMapping::class);
    }
}
