<?php


namespace App\Services\Discounts;


use App\Models\CartItem;
use App\Services\Discounts\Contracts\DiscountPriceCalculatorInterface;

class FixedPriceDiscountCalculator implements DiscountPriceCalculatorInterface
{
    public function apply(CartItem $item): float
    {
        return $item->getDiscount() * $item->getQuantity();
    }
}
