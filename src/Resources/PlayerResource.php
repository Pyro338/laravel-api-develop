<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'Player',
            'id' => (string) $this->uuid,
            'attributes' => [
                'integer_id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'email_verified_at' => ($this->email_verified_at ? $this->email_verified_at->toIso8601String() : null),
                'two_factor_enabled' => (bool) $this->two_factor_enabled,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
            'related' => [
                'avatar' => new AvatarResource($this->whenLoaded('avatar')),
                'domain' => new DomainResource($this->whenLoaded('domain')),
                'roles' => new RoleCollection($this->whenLoaded('roles')),
                'affiliate' => new UserResource($this->whenLoaded('affiliate')),
                'referrals' => new UserCollection($this->whenLoaded('referrals')),
            ],
        ];
    }
}
