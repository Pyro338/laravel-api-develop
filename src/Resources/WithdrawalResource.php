<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'withdrawal',
            'id' => (string) $this->uuid,
            'attributes' => [
                'player_id' => $this->player_id,
                'currency' => $this->currency,
                'amount' => (float) $this->amount,
                'address' => $this->address,
                'confirmed' => (bool) $this->confirmed,
                'approved' => (bool) $this->approved,
                'sent' => (bool) $this->sent,
                'cancelled' => (bool) $this->cancelled,
                'refunded' => (bool) $this->refunded,
                'transaction_uuid' => $this->transaction_uuid,
                'withdrawal_uuid' => $this->withdrawal_uuid,
                'txid' => $this->txid,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
            'related' => [
                'user' => UserResource::make($this->whenLoaded('user')),
                'player' => PlayerResource::make($this->whenLoaded('player')),
                'account' => AccountResource::make($this->whenLoaded('account')),
            ],
        ];
    }
}
