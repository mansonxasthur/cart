<?php


namespace App\Http\Resources;


class CartResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    public function toArray($request)
    {
        return [
            'Subtotal'  => $this->getFormattedSubTotal(),
            'Taxes'     => $this->getFormattedCollectedTaxes(),
            'Discounts' => $this->when(!empty($this->getDiscounts()), $this->getDiscounts()),
            'Total'     => $this->getFormattedTotal(),
        ];
    }
}
