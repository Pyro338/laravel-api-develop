<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PaybetrTransaction extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'uuid',
        'domain_id',
        'player_id',
        'category',
        'txid',
        'recipient_address',
        'currency',
        'amount',
        'converted_amount',
        'unconfirmed',
        'confirmed',
        'complete',
        'external_id',
    ];

    /**
     * Player relationship.
     */
    public function player()
    {
        return $this->belongsTo(GlobalAuth::userModel(), 'player_id', 'id');
    }
}
