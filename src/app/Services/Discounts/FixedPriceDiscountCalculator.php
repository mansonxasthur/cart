<?php


namespace App\Services\Discounts;


use App\Models\CartItem;

class FixedPriceDiscountCalculator extends DiscountPriceCalculator
{
    public function apply(CartItem &$item)
    {
        $item->setTotalPrice(
            $this->calculatePrice($item)
        );
    }

    protected function calculatePrice(CartItem $item): float
    {
        return $item->getTotalPrice() - ($item->getDiscount() * $item->getQuantity());
    }
}
