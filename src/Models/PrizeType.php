<?php

namespace Gamebetr\Api\Models;

use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PrizeType
 *
 * @property string uuid
 */
class PrizeType extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'description',
        'key_class',
    ];

}
