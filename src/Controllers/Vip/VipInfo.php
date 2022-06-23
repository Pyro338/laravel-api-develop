<?php

namespace Gamebetr\Api\Controllers\Vip;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VipInfo extends Controller
{
    /**
     * Invoke.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $domain = GlobalAuth::getDomain();
        $tiers = $domain->variable('vip_tiers');
        abort_if(empty($tiers), JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'VIP is not set up');

        $vipLevels = [];
        for ($i = 1; $i <= $tiers; $i++) {
            $vipLevels[] = [
                'tier' => $i,
                'name' => $domain->variable('vip_tier_'.$i.'_name'),
                'required_points' => $domain->variable('vip_tier_'.$i.'_points'),
                'casino_betback_percent' => $domain->variable('casino_betback_tier_'.$i.'_percent'),
                'sports_betback_percent' => $domain->variable('sports_betback_tier_'.$i.'_percent'),
                'casino_lossback_percent' => $domain->variable('casino_lossback_tier_'.$i.'_percent'),
                'sports_lossback_percent' => $domain->variable('sports_lossback_tier_'.$i.'_percent'),
            ];
        }
        $globalVipInfo = [
            'points_earned_per_casino_chip_bet' => $domain->variable('casino_status_points_earned'),
            'points_earned_per_sports_chip_bet' => $domain->variable('sports_status_points_earned'),
        ];
        return response()->json([
            'data' => [
                'type' => 'vip',
                'attributes' => [
                    'global_vip_info' => $globalVipInfo,
                    'vip_levels' => $vipLevels,
                ],
            ],
        ]);
    }
}
