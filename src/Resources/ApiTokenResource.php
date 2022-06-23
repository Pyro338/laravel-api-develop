<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiTokenResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'apiToken',
            'id' => (string) $this->uuid,
            'attributes' => [
                'api_token' => $this->api_token,
                'api_token_expiration' => $this->api_token_expiration->toIso8601String(),
                'refresh_token' => $this->refresh_token,
                'refresh_token_expiration' => $this->refresh_token_expiration->toIso8601String(),
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
