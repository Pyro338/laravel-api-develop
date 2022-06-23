<?php

namespace Gamebetr\Api\Models;

use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class GameCenterReservation extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'game_transaction_id',
        'bank_reservation_id',
    ];
}
