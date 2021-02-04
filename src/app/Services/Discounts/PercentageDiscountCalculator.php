<?php


namespace App\Services\Discounts;


use App\Models\CartItem;

class PercentageDiscountCalculator extends DiscountPriceCalculator
{
    public function apply(CartItem &$item)
    {
        $item->setTotalPrice(
            $this->calculatePrice($item)
        );
    }

    protected function calculatePrice(CartItem $item): float
    {
        $price = $item->getTotalPrice();
        $price -= ($item->getDiscount() / 100) * $price;

        return $price;
    }
}
