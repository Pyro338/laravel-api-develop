<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaybetrTransactionResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'deposit',
            'id' => (string) $this->uuid,
            'attributes' => [
                'player_id' => $this->player_id,
                'category' => $this->category,
                'txid' => $this->txid,
                'recipient_address' => $this->recipient_address,
                'currency' => $this->currency,
                'amount' => $this->amount,
                'converted_amount' => $this->converted_amount,
                'unconfirmed' => (bool) $this->unconfirmed,
                'confirmed' => (bool) $this->confirmed,
                'complete' => (bool) $this->complete,
                'external_id' => (bool) $this->external_id,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
            'related' => [
                'player' => PlayerResource::make($this->whenLoaded('player')),
            ],
        ];
    }
}
