<?php

namespace Gamebetr\Api\Models;

use DBD\Utility\Traits\HasEncryptedFields;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PaybetrApiToken extends Model
{
    use HasEncryptedFields, HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'api_token',
        'api_token_expiration',
        'refresh_token',
        'refresh_token_expiration',
    ];

    /**
     * Date fields.
     * @var array
     */
    protected $dates = [
        'api_token_expiration',
        'refresh_token_expiration',
    ];

    /**
     * Encrypted attributes.
     * @var array
     */
    protected $encrypt = [
        'api_token',
        'refresh_token',
    ];

    /**
     * Hidden attributes.
     * @var array
     */
    protected $hidden = [
        'api_token',
        'refresh_token',
    ];
}
