<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaybetrWithdrawalResource extends JsonResource
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
                'account_uuid' => $this->account_uuid,
                'withdrawal_uuid' => $this->withdrawal_uuid,
                'transaction_uuid' => $this->transaction_uuid,
                'refund_transaction_uuid' => $this->refund_transaction_uuid,
                'request_currency' => $this->request_currency,
                'converted_currency' => $this->converted_currency,
                'address' => $this->address,
                'amount' => $this->amount,
                'converted_amount' => $this->converted_amount,
                'confirmed' => (bool) $this->confirmed,
                'cancelled' => (bool) $this->cancelled,
                'approved' => (bool) $this->approved,
                'refunded' => (bool) $this->refunded,
                'sent' => (bool) $this->sent,
                'txid' => (bool) $this->txid,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
            'related' => [
                'user' => UserResource::make($this->whenLoaded('user')),
                'player' => PlayerResource::make($this->whenLoaded('player')),
            ],
        ];
    }
}
