<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
{
    /**
     * to Array.
     * @return array
     */
    public function toArray($request)
    {
        return RoleResource::collection($this->collection);
    }
}
