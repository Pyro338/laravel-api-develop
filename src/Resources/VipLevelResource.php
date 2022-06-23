<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VipLevelResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'VipLevel',
            'id' => $this->id,
            'attributes' => [
                'domain_id' => $this->domain_id,
                'level' => $this->level,
                'name' => $this->name,
                'league' => $this->league,
                'status_points_required' => $this->status_points_required,
                'color' => $this->color,
                'icon' => $this->icon,
            ],
            'related' => [
                'rates' => ['do' => 'here'],
            ],
        ];
    }
}
