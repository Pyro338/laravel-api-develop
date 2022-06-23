<?php

namespace Gamebetr\Api\Controllers\Affiliate;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AffiliateInfo extends Controller
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
        return response()->json([
            'data' => [
                'type' => 'affiliate',
                'attributes' => [
                    'affiliate_levels' => $affiliateLevels,
                ],
            ],
        ]);
    }
}
