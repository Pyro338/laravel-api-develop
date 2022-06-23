<?php

declare(strict_types=1);

namespace Gamebetr\Api\Services;

use Gamebetr\Api\Facades\User;

class LeaderboardService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function getServiceDomainKey(): string
    {
        return 'bank';
    }

    /**
     * Run the Most Bets leaderboard report.
     *
     * @param array $filters
     *   An array of filters to pass.
     * @param array $page
     *   An array of page params to pass.
     *
     * @return array
     *  The report results.
     */
    public function reportMostBets(array $filters = [], array $page = []): array
    {
        return $this->runGenericReport('reports/most-bets', $filters, $page);
    }

    /**
     * Run the Top Bet leaderboard report.
     *
     * @param array $filters
     *   An array of filters to pass.
     * @param array $page
     *   An array of page params to pass.
     *
     * @return array
     *  The report results.
     */
    public function reportTopBet(array $filters = [], array $page = []): array
    {
        return $this->runGenericReport('reports/top-bet', $filters, $page);
    }

    /**
     * Run a simple leaderboard report.
     *
     * @param string $endpoint
     *   The report endpoint.
     * @param array $filters
     *   An array of filters to pass.
     * @param array $page
     *   An array of page params to pass.
     *
     * @return array
     *  The report results.
     */
    protected function runGenericReport(string $endpoint, array $filters = [], array $page = []): array
    {
        $qp = array_filter([
            'filter' => $filters,
            'page' => $page + ['number' => 1, 'size' => '50'],
        ]);
        return User::injectPlayer($this->request('GET', $endpoint.'?'.http_build_query($qp), [], true, 5));
    }

}
