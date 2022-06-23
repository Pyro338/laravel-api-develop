<?php

namespace Gamebetr\Api\Services;

use Carbon\Carbon;
use Gamebetr\Api\Facades\Prize\FreeSpins;
use Gamebetr\Api\Facades\Prize\StatusPoints;
use Gamebetr\Api\Models\PrizeAward;
use Gamebetr\Api\Models\PrizeType;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PrizeService extends AbstractService
{

    /**
     * Create prize award.
     */
    public function awardPrize($player_id, $prize_key, $value, $optional_params = [])
    {

        $parameters = [
            'player_id' => $player_id,
            'value' => $value,
            'optional_params' => $optional_params
        ];

        $prizeClass = null;
        switch ($prize_key) {
            case 'FreeSpins': $prizeClass = FreeSpins::class; break;
            case 'StatusPoints': $prizeClass = StatusPoints::class; break;
            default: abort(404, 'Unknown prize type');
        }

        if (!$prizeType = PrizeType::where('key_class','like',$prize_key)->first()) {
            Log::info('Unknown key class value: ' . json_encode($prize_key));
            abort(404, 'Invalid prize key');
        }

        $prizeAward = PrizeAward::create([
            'name' => $optional_params['award_name'] ?? null,
            'prize_type_id' => $prizeType->id,
            'user_id' => $player_id,
            'prize_value' => $value,
        ]);
        $prizeAward->save();

        return $prizeClass::Award($parameters);
    }

    /**
     * Create prize.

     */
    public function createPrize( string $name, string $description, string $keyClassName )
    {
        return (PrizeType::firstOrCreate([
            'name' => $name,
            'description' => $description,
            'key_class' => $keyClassName
        ])->wasRecentlyCreated) ? 'New prize type is created' : 'Prize type already exists';
    }

    public function getServiceDomainKey(): string
    {
       return 'game_center';
    }
}
