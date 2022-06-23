<?php

declare(strict_types=1);

namespace Gamebetr\Api\Services;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Facades\Uri;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

abstract class AbstractService
{
    /**
     * Make a remote request.
     *
     * @param string $method
     *   The request method. One of GET, POST, PUT, HEAD, DELETE.
     * @param string $endpoint
     *   The remote endpoint.
     * @param array $parameters
     *   A key value array of POST values.
     * @param bool $cache
     *   TRUE to cache the response.
     * @param int $cacheMinutes
     *   The number of minutes to cache the response for.
     * @param array|null &$errors
     *   Any JSON response errors from a failed request.
     *
     * @return array|string
     *   The response from the remote resource.
     */
    public function request(string $method, string $endpoint, array $parameters = [], bool $cache = false, int $cacheMinutes = 0, array &$errors = null) {
        $domain = GlobalAuth::getDomain();
        $cache_key = implode('_', [
            $this->getServiceDomainKey(),
            $domain->id,
            $method,
            $endpoint,
            json_encode($parameters),
        ]);

        if ($cache === false || $cacheMinutes < 0) {
            $cacheMinutes = 0;
        }

        return $cache ?
            Cache::remember(
                $cache_key,
                $cacheMinutes * 60,
                function () use ($method, $endpoint, $parameters, $errors) {
                    return $this->uncachedRequest(
                        $method,
                        $endpoint,
                        $parameters,
                        $errors
                    );
                }
            ) :
            $this->uncachedRequest($method, $endpoint, $parameters, $errors);
    }

    /**
     * Get the service key.
     *
     * This is used for determining the base URI and building cache keys.
     *
     * @return string
     *   The service key.
     */
    abstract public function getServiceDomainKey(): string;

    /**
     * Makes an uncahced request to a remote service.
     *
     * @param string $method
     *   The request method. One of GET, POST, PUT, HEAD, DELETE.
     * @param string $endpoint
     *   The remote endpoint.
     * @param array $parameters
     *   A key value array of POST values.
     * @param array|null &$errors
     *   Any JSON response errors from a failed request.
     *
     * @return array|string
     *   The response from the remote resource.
     */
    protected function uncachedRequest(string $method, string $endpoint, array $parameters = [], array &$errors = null)
    {
        $client = new Client();
        try {
            $response = $client
                ->request($method, $this->getBaseUri().'/'.ltrim($endpoint, '/'), [
                    'headers' => [
                        'User-Agent' => 'api-service|'.parse_url(URL::to('/'), PHP_URL_HOST).'|'.get_class($this),
                        'Accept' => 'application/vnd.api+json',
                        'Content-Type' => 'application/vnd.api+json',
                        'Authorization' => 'Bearer '.$this->getApiToken(),
                    ],
                    'json' => $parameters,
                ])
                ->getBody()
                ->getContents();

            return $this->processResponse($response);
        }
        catch (BadResponseException $e) {
            if ($e->hasResponse()) {
                $errors = $this->processResponse($e->getResponse()->getBody()->getContents()) ?? [];
            }
            abort($e->getCode(), empty($errors) ? $e->getMessage() : json_encode($errors));
        }
        catch (Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Get base uri.
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return Uri::baseUri($this->getServiceDomainKey());
    }

    /**
     * Get the API token.
     *
     * @return string
     *   The API token.
     */
    protected function getApiToken(): string
    {
        return Uri::token();
    }

    /**
     * Post-process the response from the request.
     *
     * Some service implementations do different things with the response, so
     * this is a way to let them leverage this class without rewriting their
     * methods.
     *
     * @param $response
     *   The response from the request.
     *
     * @return mixed
     *   By default, the json_decoded value as an associative array.
     */
    protected function processResponse($response)
    {
        return json_decode($response, true);
    }

    /**
     * Check if the active user is a domain admin.
     *
     * @return bool
     *   TRUE if they are a domain admin.
     */
    protected function isDomainAdmin(): bool
    {
        return Auth::user() && !empty(Auth::user()->domain_admin);
    }
}
