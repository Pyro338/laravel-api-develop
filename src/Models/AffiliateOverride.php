<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class AffiliateOverride extends Model
{

    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'user_id',
        'level',
    ];

    /**
     * Belongs to user.
     */
    public function user()
    {
        return $this->belongsTo(GlobalAuth::userModel());
    }
}
