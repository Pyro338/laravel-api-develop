<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    use HasUuid;

    protected $fillable = [
        'extension',
    ];

    /**
     * Belongs to User
     */
    public function user()
    {
        return $this->belongsTo(GlobalAuth::userModel());
    }
}
