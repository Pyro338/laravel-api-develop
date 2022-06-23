<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'ticket',
            // 'id' => (string) $this->uuid,
            'todo' => 'yes',
            'attributes' => [
                // 'domain' => $this->domain,
                // 'name' => $this->name,
                // 'email' => $this->email,
                // 'created_at' => $this->created_at->toIso8601String(),
                // 'updated_at' => $this->updated_at->toIso8601String(),
            ],
            'related' => [
                // 'hosts' => HostResource::collection($this->whenLoaded('hosts')),
            ],
        ];
    }
}
