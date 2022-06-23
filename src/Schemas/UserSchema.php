<?php

namespace Gamebetr\Api\Schemas;

use Neomerx\JsonApi\Schema\SchemaProvider;

class UserSchema extends SchemaProvider
{
    /**
     * Resource type.
     * @var string
     */
    protected $resourceType = 'users';

    /**
     * Get id.
     * @param \Gamebetr\Api\Models\User $resource
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * Get attributes.
     * @param \Gamebetr\Api\Models\User $resource
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'integerId' => $resource->id,
            'name' => $resource->name,
            'email' => $resource->email,
            'emailVerifiedAt' => $resource->email_verified_at,
            'twoFactorEnabled' => $resource->two_factor_enabled,
            'domainAdmin' => (bool) $resource->domain_admin,
            'globalAdmin' => (bool) $resource->global_admin,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
        ];
    }

    /**
     * Get relationships.
     * @param \Gamebetr\Api\Models\User $resource
     * @param bool $isPrimary
     * @param array $includeRelationships
     * @return array
     */
    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        return [
            'domain' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
            ],
        ];
    }
}
