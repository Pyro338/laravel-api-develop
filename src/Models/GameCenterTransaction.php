<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Models\Domain;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class GameCenterTransaction extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'game',
        'game_session_id',
        'game_transaction_id',
        'account_id',
        'bank_transaction_id',
        'amount',
        'currency',
    ];

    /**
     * Domain relationship.
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
