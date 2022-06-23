<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateClickResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'AffiliateClick',
            'id' => $this->id,
            'attributes' => [
                'domain_id' => $this->domain_id,
                'player_id' => $this->player_id,
                'template_id' => $this->template_id,
                'custom_id' => $this->custom_id,
                'user_agent' => $this->user_agent,
                'referer' => $this->referer,
                'hostname' => $this->hostname,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
