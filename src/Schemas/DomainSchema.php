<?php

namespace Gamebetr\Api\Schemas;

use Neomerx\JsonApi\Schema\SchemaProvider;

class DomainSchema extends SchemaProvider
{
    /**
     * Resource type.
     * @var string
     */
    protected $resourceType = 'domains';

    /**
     * Get id.
     * @param \Gamebetr\Api\Models\Domain $resource
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * Get attributes.
     * @param \Gamebetr\Api\Models\Domain $resource
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'domain' => $resource->domain,
            'name' => $resource->name,
            'email' => $resource->email,
            'createdAt' => $resource->created_at,
            'updatedAt' => $resource->updated_at,
        ];
    }

    /**
     * Get relationships.
     * @param \Gamebetr\Api\Models\Domain $resource
     * @param bool $isPrimary
     * @param array $includeRelationships
     * @return array
     */
    public function getRelationships($resource, $isPrimary, array $includeRelationships)
    {
        return [
            'users' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
            ],
        ];
    }
}
