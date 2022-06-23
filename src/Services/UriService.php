<?php

namespace Gamebetr\Api\Services;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Exceptions\UnknownDomain;
use Gamebetr\Api\Exceptions\UnknownServiceUrl;

class UriService
{
    /**
     * Base Uri.
     * @param string $service
     * @return string
     */
    public function baseUri(string $service) : string
    {
        if (!$domain = GlobalAuth::getDomain()) {
            throw new UnknownDomain();
        }
        if (!$serviceUrl = $domain->variable($service.'_service_url')) {
            throw new UnknownServiceUrl();
        }

        return $serviceUrl;
    }

    /**
     * Token
     * @return string
     */
    public function token() : ?string
    {
        return GlobalAuth::getApiToken();
    }
}
