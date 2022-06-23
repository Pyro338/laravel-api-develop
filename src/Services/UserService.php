<?php

namespace Gamebetr\Api\Services;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;

class UserService
{
    /**
     * Find a user by id.
     * @param int $id
     * @return Authenticatable
     */
    public function find(int $id) {
        return Cache::remember('user_'.$id, Carbon::now()->addMinutes(60), function() use ($id) {
            return GlobalAuth::findUserViaIntegerId($id);
        });
    }

    /**
     * Find a user by uuid.
     * @param string $uuid
     * @return Authenticatable
     */
    public function uuid(string $uuid) {
        return Cache::remember('user_'.$uuid, Carbon::now()->addMinutes(60), function() use ($uuid) {
            return GlobalAuth::getUser($uuid);
        });
    }

    /**
     * Inject player data.
     * @param mixed $data
     * @return mixed
     */
    public function injectPlayer($data) {
        if(is_object($data)) {
            $data = json_decode(json_encode($data), true);
            $this->findPlayerId($data);
            $data = json_decode(json_encode($data));
        }
        elseif(is_array($data)) {
            $this->findPlayerId($data);
        }

        return $data;
    }

    /**
     * Add player information where a player ID is found.
     *
     * @param array &$data
     *   The data to scan and modify.
     */
    protected function findPlayerId(array &$data): void
    {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = json_decode(json_encode($value), true);
                $this->findPlayerId($data[$key]);
                $data[$key] = json_decode(json_encode($data[$key]));
            }
            elseif (is_array($value)) {
                $this->findPlayerId($data[$key]);
            }
            if (in_array($key, ['player-id', 'player_id']) && filter_var($value, FILTER_VALIDATE_INT)) {
                $player = $this->find($value);
                if ($player !== null) {
                    $data['player'] = ['name' => $player['name'] ?? 'anonymous'];
                }
            }
        }
    }
}
