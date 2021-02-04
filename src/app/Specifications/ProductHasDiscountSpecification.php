<?php


namespace App\Specifications;



use App\Models\CartItem;
use App\Specifications\Contracts\ProductSpecificationInterface;

class ProductHasDiscountSpecification implements ProductSpecificationInterface
{
    public function isSatisfiedBy(CartItem $item): bool
    {
        return $item->getDiscount() !== null;
    }
}
