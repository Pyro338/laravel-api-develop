<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PaybetrWithdrawal extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'player_id',
        'account_uuid',
        'withdrawal_uuid',
        'transaction_uuid',
        'refund_transaction_uuid',
        'request_currency',
        'converted_currency',
        'address',
        'amount',
        'converted_amount',
        'confirmed',
        'cancelled',
        'approved',
        'refunded',
        'sent',
        'txid',
    ];

    /**
     * Player relationship.
     */
    public function player()
    {
        return $this->belongsTo(GlobalAuth::userModel(), 'player_id', 'id');
    }
}
