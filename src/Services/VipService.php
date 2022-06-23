<?php

namespace Gamebetr\Api\Services;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Jobs\CreateBankAccount;
use Illuminate\Http\JsonResponse;

// use Gamebetr\Api\Models\VipLevels;

class VipService
{
    const ALLOWED_TYPES = [
        'casino',
        'sports',
    ];

    /**
     * Player.
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected $player;

    /**
     * Domain.
     * @var \DBD\GlobalAuth\Models\Domain
     */
    protected $domain;

    /**
     * Get status points account
     * @param int $playerId
     * @return array
     */
    public function getAccount(int $playerId) : ?array
    {
        $account = Bank::getAccountByPlayerIdAndType($playerId, 'status_points');
        if(isset($account['data'])) {
            $account = $account['data'];
        }

        return $account;
    }

    /**
     * Get status points balance
     * @param int $playerId
     * @return float
     */
    public function getBalance(int $playerId) : float
    {
        $this->setDomain($playerId);
        if (!$account = $this->getAccount($playerId)) {
            return 0;
        }

        return $account['attributes']['available-balance'];
    }

    /**
     * Get status points level
     * @param int $playerId
     * @return int
     */
    public function getLevel(int $playerId) : int
    {
        $this->setDomain($playerId);
        $balance = $this->getBalance($playerId);
        if (!$tiers = $this->domain->variable('vip_tiers')) {
            return 1;
        }
        $tier = 1;
        for ($i = 1; $i <= $tiers; $i++) {
            $minimum = $this->domain->variable('vip_tier_'.$i.'_points');
            if ($balance >= $minimum) {
                $tier = $i;
            }
        }

        return $tier;
    }

    /**
     * Get status points level name
     * @param int $playerId
     * @return string
     */
    public function getName(int $playerId) : string
    {
        $this->setDomain($playerId);
        $level = $this->getLevel($playerId);
        if (!$name = $this->domain->variable('vip_tier_'.$level.'_name')) {
            throw new Exception('Tier name not found');
        }

        return $name;
    }

    /**
     * Get status points earned
     * @param int $playerId
     * @return float
     */
    public function getStatusPointsEarnedPerChipBet(int $playerId, string $type) : float
    {
        $this->setDomain($playerId);
        if (!$earned = $this->domain->variable($type.'_status_points_earned')) {
            return 0;
        }

        return $earned;
    }

    /**
     * Add points.
     * @param int $playerId
     * @param float $points
     * @return array
     */
    public function addPoints(int $playerId, float $points) : array
    {
        abort(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Method not implemented');
        $this->setDomain($playerId);
        $account = $this->getAccount($playerId);
        $transaction = Bank::createTransaction(
            $account['id'],
            $points,
            null,
            'vip'
        );

        return $transaction;
    }

    /**
     * Set domain
     * @param int $playerId
     * @return void
     */
    private function setDomain(int $playerId)
    {
        if ($this->player && $this->domain) {
            if ($this->player->id == $playerId) {
                return;
            }
        }
        if (!$this->player = GlobalAuth::userModel()::with('domain')->where('id', $playerId)->first()) {
            throw new Exception('Unkown player');
        }
        $this->domain = $this->player->domain;
        GlobalAuth::setDomain($this->domain);
    }
}
