<?php

namespace Gamebetr\Api\Rules;

use Illuminate\Contracts\Validation\Rule;

class UserExists implements Rule
{
    protected $domainId;

    public function __construct($domainId)
    {
        $this->domainId = $domainId;
    }

    public function passes($attribute, $value)
    {
    }

    public function message()
    {
        return ':attribute does not exist.';
    }
}
