<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'referral',
            'id' => (string) $this->uuid,
            'attributes' => [
                'name' => $this->name,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
            'related' => [
                'avatar' => new AvatarResource($this->whenLoaded('avatar')),
            ],
        ];
    }
}
