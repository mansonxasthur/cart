<?php


namespace App\Services\Discounts;


use App\Models\CartItem;
use App\Services\Discounts\Contracts\DiscountPriceCalculatorInterface;

class PercentageDiscountCalculator implements DiscountPriceCalculatorInterface
{
    public function apply(CartItem $item): float
    {
        return ($item->getDiscount() / 100) * $item->getTotalPrice();
    }
}
