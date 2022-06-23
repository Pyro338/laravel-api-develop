<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'user_id',
        'player_id',
        'account_id',
        'currency',
        'amount',
        'address',
        'confirmed',
        'approved',
        'sent',
        'cancelled',
        'refunded',
        'transaction_uuid',
        'withdrawal_uuid',
        'txid',
    ];

    /**
     * User relationship.
     */
    public function user()
    {
        return $this->belongsTo(GlobalAuth::userModel());
    }

    /**
     * Player relationship.
     */
    public function player()
    {
        return $this->belongsTo(GlobalAuth::userModel(), 'player_id', 'id');
    }

    /**
     * Account relationship.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
