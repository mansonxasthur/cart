<?php


namespace App\Http\Resources;

/**
 * Class OffersResource
 * @package App\Http\Resources
 * @mixin \App\Models\Offer
 */
class OffersResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'offeree' => $this->getOfferee(),
            'offerable' => $this->getOfferable(),
            'discount_value' => $this->getDiscount()->getValue(),
            'discount_type' => $this->getDiscount()->getType(),
            'quantity' => $this->getQuantity(),
        ];
    }
}
