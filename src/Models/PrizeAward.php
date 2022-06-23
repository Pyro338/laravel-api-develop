<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PrizeAward
 *
 * @property string uuid
 */
class PrizeAward extends Model
{
    use HasUuid;

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'user_id',
        'prize_type_id',
        'prize_value',
        'accepted_at',
    ];

    /**
     * Belongs to user.
     */
    public function user()
    {
        return $this->belongsTo(GlobalAuth::userModel());
    }

    /**
     * Belongs to prize_type.
     */
    public function prizeType()
    {
        return $this->belongsTo(PrizeType::class);
    }
}
