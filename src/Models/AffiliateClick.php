<?php

namespace Gamebetr\Api\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{

    protected $fillable = [
        'domain_id',
        'user_agent',
        'referer',
        'hostname',
        'player_id',
        'template_id',
        'custom_id'
    ];
}
