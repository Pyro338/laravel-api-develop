<?php

namespace Gamebetr\Api\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateConversion extends Model
{

    protected $fillable = [
        'domain_id',
        'player_id',
        'affiliate_id',
        'template_id',
        'custom_id',
        'promo_code',
    ];
}
