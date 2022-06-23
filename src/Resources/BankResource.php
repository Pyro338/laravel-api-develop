<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'banks',
            'id' => (string) $this->uuid,
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
                'hidden' => (bool) $this->hidden,
                'transferable' => (bool) $this->transferable,
                'relaxed_balances' => (bool) $this->relaxed_balances,
                'currency' => $this->currency,
                'currency_type' => $this->currency_type,
                'deposit_currency' => $this->deposit_currency,
                'playable' => (bool) $this->playable,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
