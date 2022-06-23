<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReferralCollection extends ResourceCollection
{
    /**
     * to Array.
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => ReferralResource::collection($this->collection),
            'count' => $this->count(),
        ];
    }
}
