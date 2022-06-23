<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * to Array.
     * @return array
     */
    public function toArray($request)
    {
        return UserResource::collection($this->collection);
    }
}
