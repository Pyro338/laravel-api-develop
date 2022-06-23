<?php

namespace Gamebetr\Api\Services;

use Illuminate\Contracts\Auth\Authenticatable;

class UserSingleton
{
    /**
     * Target user.
     * @var \Illuminate\Auth\Contracts\Authenticatable
     */
    protected $user;

    /**
     * Set user.
     * @param \Illuminate\Auth\Contracts\Authenticatable $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * Get user.
     * @return null|\Illuminate\Auth\Contracts\Authenticatable
     */
    public function getUser() : ?Authenticatable
    {
        return $this->user;
    }
}
