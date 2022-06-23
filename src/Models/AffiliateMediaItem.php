<?php

namespace Gamebetr\Api\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateMediaItem extends Model
{

    protected $table = 'affiliate_media_item';

    protected $fillable = [
        'domain_id',
        'media_group_id',
        'title',
        'filepath',
        'weight',
    ];

    public function mediaGroup()
    {
        return $this->belongsTo(AffiliateMediaGroup::class, 'media_group_id', 'id');
    }
}
