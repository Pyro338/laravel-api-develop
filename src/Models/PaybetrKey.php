<?php

namespace Gamebetr\Api\Models;

use DBD\Utility\Traits\HasEncryptedFields;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PaybetrKey extends Model
{
    use HasEncryptedFields, HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'user_id',
        'key',
    ];

    /**
     * Encrypted fields.
     * @var array
     */
    protected $encrypt = [
        'key',
    ];
}
