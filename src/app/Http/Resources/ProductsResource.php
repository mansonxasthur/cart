<?php


namespace App\Http\Resources;


/**
 * Class ProductsResource
 * @package App\Http\Resources
 * @mixin \App\Models\Product
 */
class ProductsResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'discount' => $this->getDiscount(),
            'discount_type' => $this->getDiscountType()
        ];
    }
}
