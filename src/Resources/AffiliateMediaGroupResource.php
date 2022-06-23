<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateMediaGroupResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'AffiliateMediaGroup',
            'id' => $this->id,
            'attributes' => [
                'domain_id' => $this->domain_id,
                'title' => $this->title,
                'landing_page_url' => $this->landing_page_url,
                'weight' => $this->weight,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
