<?php


namespace App\Http\Resources;


/**
 * Class CartResource
 * @package App\Http\Resources
 * @mixin \App\Models\Cart;
 */
class CartResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    public function toArray($request)
    {
        $discounts = $this->getDiscounts();
        return [
            'Subtotal'  => $this->getFormattedSubTotal(),
            'Taxes'     => $this->getFormattedCollectedTaxes(),
            'Discounts' => $this->when(!empty($discounts), $discounts),
            'Total'     => $this->getFormattedTotal(),
        ];
    }
}
