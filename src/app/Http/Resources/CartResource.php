<?php


namespace App\Http\Resources;


class CartResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    public function toArray($request)
    {
        return [
            'Subtotal' => $this->getFormattedSubTotal(),
            'Taxes' => $this->getFormattedTaxes(),
            'Discounts' => $this->getDiscounts(),
            'Total' => $this->getFormattedTotal(),
        ];
    }
}
