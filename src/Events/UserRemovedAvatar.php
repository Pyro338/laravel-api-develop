<?php

namespace Gamebetr\Api\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;

class UserRemovedAvatar
{
    use SerializesModels;

    /**
     * User.
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * Class constructor.
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return void
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
