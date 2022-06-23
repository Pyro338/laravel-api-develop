<?php

namespace Gamebetr\Api\Services;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Exceptions\ValidationError;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Models\AffiliateClick;
use Gamebetr\Api\Models\AffiliateConversion;
use Gamebetr\Api\Models\AffiliateMediaGroup;
use Gamebetr\Api\Models\AffiliateMediaItem;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AffiliateService
{
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
     * Get affiliate account
     * @param int $playerId
     * @return array
     */
    public function getAccount(int $playerId) : ?array
    {
        $account = Bank::getAffiliateAccount($playerId);
        if(isset($account['data'])) {
            $account = $account['data'];
        }

        return $account;
    }

    /**
     * Get affiliate balance
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
     * Get affiliate level
     * @param int $playerId
     * @return int
     */
    public function getLevel(int $playerId) : int
    {
        if(!$user = GlobalAuth::userModel()::find($playerId)) {
            return 0;
        }
        if($user->affiliateOverride) {
            return $user->affiliateOverride->level;
        }
        $this->setDomain($playerId);
        $earnings = $this->getLast30DaysEarnings($playerId);
        if (!$tiers = $this->domain->variable('affiliate_tiers')) {
            return 0;
        }
        $tier = 0;
        for ($i = 1; $i <= $tiers; $i++) {
            $minimum = $this->domain->variable('affiliate_tier_'.$i.'_earning_minimum');
            if ($earnings >= $minimum) {
                $tier = $i;
            }
        }

        return $tier;
    }

    /**
     * Get last 30 days earnings
     * @param int $userId
     */
    public function getLast30DaysEarnings(int $userId)
    {
        $account = $this->getAccount($userId);
        return Bank::getAccountWinLoss($account['id'], ['filter' => ['date-start' => Carbon::now()->subDays(30)]]);
    }

    /**
     * List users
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listUsers(Authenticatable $user) : Collection
    {
        return $user->referrals;
    }

    public function listMedia()
    {
        $media = AffiliateMediaGroup::where('domain_id', 123)->with('items')->orderBy('created_at', 'DESC')->take(10)->get();
        return $media;
    }

    public function saveClick($data)
    {
        // $data['player_id'] = (int) Auth::id();
        // $data['domain_id'] = (int) Auth::user()->domain_id;

        if (!$data['domain_id'] || !$data['player_id']) {
            throw new ValidationError();
        }
        // $referer = $_SERVER['HTTP_REFERER'] ?? '';

        $AffiliateClick = AffiliateClick::create([
            'domain_id' => $data['domain_id'] ?? 0,
            'player_id' => $data['player_id'] ?? 0,
            'template_id' => $data['template_id'] ?? 0,
            'custom_id' => $data['custom_id'] ?? '',
            'user_agent' => isset($data['user_agent']) ? substr($data['user_agent'], 0, 254) : '',
            'referer' => isset($data['referer']) ? substr($data['referer'], 0, 254) : '',
            'hostname' => $data['hostname'] ?? '',

        ]);

        $AffiliateClick->save();

        return $AffiliateClick;
    }

    public function saveConversion($data)
    {
        if (!$data['domain_id'] || !$data['player_id'] || !is_int($data['domain_id'])) {
            throw new ValidationError();
        }

        $conversion = AffiliateConversion::create([
            'domain_id' => $data['domain_id'] ?? 0,
            'player_id' => $data['player_id'] ?? 0,
            'affiliate_id' => $data['affiliate_id'] ?? 0,
            'template_id' => $data['template_id'] ?? '',
            'custom_id' => $data['custom_id'] ?? '',
            'promo_code' => $data['promo_code'] ?? '',
        ]);

        $conversion->save();

        return $conversion;
    }

    public function saveUser($data)
    {
        userVariable(['affiliate_parent_id' => $data['affiliate_user_id']], $data['user_id']);
        $user = userVariable('affiliate_parent_id', $data['user_id']);
        return $user;
    }

    public function saveMediaGroup($data)
    {
        $affiliateMediaGroup = AffiliateMediaGroup::create([
            'domain_id' => $data['domain_id'],
            'title' => $data['title'],
            'landing_page_url' => $data['landing_page_url'],
            'weight' => $data['weight'] ?? 0,
        ]);

        $affiliateMediaGroup->save();

        return $affiliateMediaGroup;
    }

    public function saveMediaItem($data)
    {
        $affiliateMediaItem = AffiliateMediaItem::create([
            'domain_id' => $data['domain_id'],
            'media_group_id' => $data['media_group_id'],
            'title' => $data['title'],
            'filepath' => $data['filepath'],
            'weight' => $data['weight'] ?? 0,
        ]);

        $affiliateMediaItem->save();

        return $affiliateMediaItem;
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
