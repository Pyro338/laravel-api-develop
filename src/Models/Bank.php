<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Bank
 *
 * @property string uuid
 */
class Bank extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'description',
        'hidden',
        'transferable',
        'relaxed_balances',
        'currency',
        'currency_type',
        'deposit_currency',
        'playable',
    ];

    /**
     * Casted attributes.
     * @var array
     */
    protected $casts = [
        'hidden' => 'boolean',
        'transferable' => 'boolean',
        'relaxed_balances' => 'boolean',
        'playable' => 'boolean',
    ];

    /**
     * Belongs to user.
     */
    public function user()
    {
        return $this->belongsTo(GlobalAuth::userModel());
    }
}
