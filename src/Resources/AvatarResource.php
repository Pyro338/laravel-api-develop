<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AvatarResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $links = [];
        $links['original'] = url('/avatar/'.$this->uuid.'.'.$this->extension);
        foreach (config('api.available_avatar_sizes', []) as $size) {
            $links[$size] = url('/avatar/'.$this->uuid.'_'.$size.'.'.$this->extension);
        }

        return [
            'type' => 'Avatar',
            'id' => (string) $this->uuid,
            'attributes' => [
                'filename' => $this->uuid,
                'extension' => $this->extension,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
            'links' => $links,
            'related' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
