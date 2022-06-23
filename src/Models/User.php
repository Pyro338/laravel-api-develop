<?php

namespace Gamebetr\Api\Models;

use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\User as GlobalAuthUser;
use Illuminate\Database\Eloquent\Relations\HasOne;

abstract class User extends GlobalAuthUser
{
    /**
     * Fillable attributes.
     * @var array
     */
    protected $fillable = [
        'id',
        'uuid',
        'domain_id',
        'name',
        'email',
        'email_verified_at',
        'two_factor_enabled',
        'domain_admin',
        'global_admin',
        'enabled',
        'affiliate_id',
    ];

    /**
     * Always include roles
     */
    protected $with = [
        'roles',
    ];

    /**
     * Has one AffiliateOverride
     *
     * @return HasOne
     */
    public function affiliateOverride(): HasOne
    {
        return $this->hasOne(AffiliateOverride::class);
    }

    /**
     * Has one Avatar.
     */
    public function avatar()
    {
        return $this->hasOne(Avatar::class);
    }

    /**
     * Affiliate relationship.
     */
    public function affiliate()
    {
        return $this->belongsTo(GlobalAuth::userModel(), 'affiliate_id', 'id');
    }

    /**
     * Referrals relationship.
     */
    public function referrals()
    {
        return $this->hasMany(GlobalAuth::userModel(), 'affiliate_id', 'id');
    }

    /**
     * Drupal mappings relationship.
     */
    public function drupalMappings()
    {
        return $this->hasMany(DrupalMapping::class, 'user_uuid', 'uuid');
    }
}
