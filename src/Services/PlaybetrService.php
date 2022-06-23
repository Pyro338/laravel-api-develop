<?php

namespace Gamebetr\Api\Services;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Exceptions\PlaybetrApiTokenNotFound;
use Gamebetr\Api\Exceptions\UnknownDomain;

class PlaybetrService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function getServiceDomainKey(): string
    {
        return 'playbetr';
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnknownDomain
     * @throws PlaybetrApiTokenNotFound
     */
    public function getApiToken(): string
    {
        if (!$domain = GlobalAuth::getDomain()) {
            throw new UnknownDomain();
        }
        if (!$apiToken = $domain->variable('playbetr_api_token')) {
            throw new PlaybetrApiTokenNotFound();
        }

        return $apiToken;
    }

    /**
     * {@inheritdoc}
     */
    protected function processResponse($response)
    {
        return json_decode($response, false);
    }
}
