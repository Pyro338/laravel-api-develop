<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateMediaItemResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'AffiliateMediaItem',
            'id' => $this->id,
            'attributes' => [
                'domain_id' => $this->domain_id,
                'media_group_id' => $this->media_group_id,
                'title' => $this->title,
                'filepath' => $this->filepath,
                'weight' => $this->weight,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
