<?php

declare(strict_types=1);

namespace Gamebetr\Api\Services\Bank;

use Gamebetr\Api\Services\AbstractService;

class Tags extends AbstractService
{

    /**
     * @inheritDoc
     */
    public function getServiceDomainKey(): string
    {
        return 'bank';
    }

    /**
     * Get a list of available tags.
     *
     * @param array $query
     *   Optional parameters to pass with the request.
     *
     * @return array|string
     *   The request result.
     */
    public function listTags(array $query = [])
    {
        return $this->request('GET', 'tags?'.http_build_query($query), [], true, 5);
    }

    /**
     * Get a specific tag.
     *
     * @param string $uuid
     *   The tag UUID.
     * @param array $query
     *   Optional parameters to pass with the request.
     *
     * @return array|string
     *   The request result.
     */
    public function getTag(string $uuid, array $query = [])
    {
        return $this->request('GET', 'tags/'.$uuid.'?'.http_build_query($query), [], true, 5);
    }
}
