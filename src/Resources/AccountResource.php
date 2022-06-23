<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'account',
            'id' => (string) $this->uuid,
            'attributes' => [
                'player_id' => $this->player_id,
                'name' => $this->name,
                'description' => $this->description,
                'hidden' => (bool) $this->hidden,
                'transferable' => (bool) $this->transferable,
                'relaxed_balances' => (bool) $this->relaxed_balances,
                'currency' => $this->currency,
                'currency_type' => $this->currency_type,
                'deposit_currency' => $this->deposit_currency,
                'playable' => (bool) $this->playable,
                'primary' => (bool) $this->primary,
                'balance' => $this->when(!is_null($this->balance), $this->balance),
                'available_balance' => $this->when(!is_null($this->available_balance), $this->available_balance),
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
