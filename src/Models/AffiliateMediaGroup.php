<?php

namespace Gamebetr\Api\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateMediaGroup extends Model
{

    protected $table = 'affiliate_media_group';

    protected $fillable = [
        'domain_id',
        'title',
        'landing_page_url',
        'weight',
    ];

    public function items()
    {
        return $this->hasMany(AffiliateMediaItem::class, 'media_group_id', 'id');
    }
}
