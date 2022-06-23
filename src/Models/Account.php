<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'player_id',
        'name',
        'description',
        'hidden',
        'transferable',
        'relaxed_balances',
        'currency',
        'currency_type',
        'deposit_currency',
        'playable',
        'primary',
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
        'primary' => 'boolean',
    ];

    /**
     * Boot override.
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
            if (in_array('primary', array_keys($model->getDirty()))) {
                static::where('player_id', $model->player_id)->update(['primary' => false]);
            }
        });
    }

    /**
     * Belongs to user.
     */
    public function user()
    {
        return $this->belongsTo(GlobalAuth::userModel());
    }

    /**
     * Belongs to player.
     */
    public function player()
    {
        return $this->belongsTo(GlobalAuth::userModel(), 'player_id', 'id');
    }

    /**
     * Has many withdrawals.
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
}
