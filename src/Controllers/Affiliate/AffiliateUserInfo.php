<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\Affiliate;
use Gamebetr\Api\Facades\UserSingleton;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AffiliateUserInfo extends Controller
{
    /**
     * Invoke.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $domain = GlobalAuth::getDomain();
        $tiers = $domain->variable('affiliate_tiers');
        abort_if(empty($tiers), JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Affiliate is not set up');

        $affiliateLevels = [];
        for ($i = 1; $i <= $tiers; $i++) {
            $affiliateLevels[] = [
                'tier' => $i,
                'last_30_days_earning_minimum' => $domain->variable('affiliate_tier_'.$i.'_earning_minimum'),
                'casino_loss_payout_percent' => $domain->variable('casino_affiliate_tier_'.$i.'_payout'),
                'sports_loss_payout_percent' => $domain->variable('sports_affiliate_tier_'.$i.'_payout'),
            ];
        }
        $user = UserSingleton::getUser();
        $userTier = Affiliate::getLevel($user->id);
        $userAffiliateInfo = [
            'tier' => $userTier,
            'balance' => Affiliate::getBalance($user->id),
            'referrals' => $user->referrals()->count(),
            'last_30_days_earnings' => Affiliate::getLast30DaysEarnings($user->id),
            'casino_loss_payout_percent' => $domain->variable('casino_affiliate_tier_'.$userTier.'_payout'),
            'sports_loss_payout_percent' => $domain->variable('sports_affiliate_tier_'.$userTier.'_payout'),
        ];
        return response()->json([
            'data' => [
                'type' => 'affiliate',
                'attributes' => [
                    'user_affiliate_info' => $userAffiliateInfo,
                    'affiliate_levels' => $affiliateLevels,
                ],
            ],
        ]);
    }
}
