<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Illuminate\Database\Eloquent\Model;

class DrupalMapping extends Model
{
    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'drupal_id',
        'user_uuid',
        'domain_id',
    ];


    /**
     * User relationship.
     */
    public function user()
    {
        return $this->belongsTo(GlobalAuth::userModel(), 'user_uuid', 'uuid');
    }

    /**
     * Domain relationship.
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
