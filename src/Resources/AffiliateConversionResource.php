<?php

namespace Gamebetr\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateConversionResource extends JsonResource
{
    /**
     * To array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'AffiliateConversion',
            'id' => $this->id,
            'attributes' => [
                'domain_id' => $this->domain_id,
                'player_id' => $this->player_id,
                'affiliate_id' => $this->affiliate_id,
                'template_id' => $this->template_id,
                'custom_id' => $this->custom_id,
                'promo_code' => $this->promo_code,
            ],
        ];
    }
}
