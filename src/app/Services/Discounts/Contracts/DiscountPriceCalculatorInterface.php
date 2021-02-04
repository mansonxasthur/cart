<?php


namespace App\Services\Discounts\Contracts;


use App\Models\CartItem;

interface DiscountPriceCalculatorInterface
{
    public function apply(CartItem &$item);
}
