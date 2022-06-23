<?php

declare(strict_types=1);

namespace Gamebetr\Api\Services;

use Gamebetr\Api\Facades\User;
use Illuminate\Support\Facades\Auth;

class SupportService extends AbstractService
{

    /**
     * {@inheritdoc}
     */
    public function getServiceDomainKey(): string
    {
        return 'content';
    }

    /**
     * List of support tickets from content API.
     *
     * @param array $input
     *   An array of request input to pass on.
     *
     * @return array
     *   The service response.
     */
    public function listTickets(array $input = []): array
    {
        $input['filter'] = $this->validatePlayerId($input['filter'] ?? []);

        $qp = http_build_query($input);

        return $this->request('GET', 'content?'.$qp);
    }

    /**
     * Forces the Auth ID to the player_id when not a domain admin.
     *
     * @param array $params
     *   The params that will be used for filters or POST.
     *
     * @return array
     *   The modified params.
     */
    protected function validatePlayerId(array $params): array
    {
        // Cast player_id to an int so we can do a type strict check.
        if (!empty($params['player_id'])) {
            $params['player_id'] = (int)$params['player_id'];
        }

        $auth_id = (int)Auth::id();

        // Only domain admins are allowed to not filter by their own player id.
        if ((empty($params['player_id']) || $params['player_id'] !== $auth_id) && !$this->isDomainAdmin()) {
            $params['player_id'] = $auth_id;
        }

        return $params;
    }

    /**
     * Get support ticket form content API based on provide uuid.
     *
     * @param string $uuid
     *   A support ticket UUID.
     * @param array $input
     *   Any request input to pass along.
     *
     * @return array
     *   The service response.
     */
    public function getTicket(string $uuid, array $input = []): array
    {
        $qp = http_build_query($input);

        return $this->request('GET', sprintf('content/%s?%s', $uuid, $qp));
    }

    /**
     * Create support ticket on content API.
     *
     * @param string $title
     *   The ticket title.
     * @param string $body
     *   The ticket body.
     * @param array $params
     *   Optional parameters when creating the ticket. A keyed array containing
     *   zero or more of tags, parent_uuid, and player_id.
     *
     * @return array
     *   The service response.
     */
    public function createTicket(string $title, string $body, array $params = []): array
    {
        $params = collect($params)
            ->put('title', $title)
            ->put('body', $body)
            ->toArray();
        $params = $this->validatePlayerId($params);

        return $this->request('POST', 'content', $params);
    }

    /**
     * Update an existing ticket.
     *
     * @param string $uuid
     *   The uuid of the ticket to update.
     * @param array $input
     *   The values to update on the ticket.
     *
     * @return array
     *   The response from the service.
     */
    public function updateTicket(string $uuid, array $input): array
    {
        return $this->request('PUT', sprintf('content/%s', $uuid), $input);
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $endpoint, array $parameters = [], bool $cache = false, int $cacheMinutes = 0, array &$errors = null) {
        $response = parent::request($method, $endpoint, $parameters, $cache, $cacheMinutes, $errors);
        return User::injectPlayer($response);
    }
}
