<?php


namespace App\Services\Discounts;


use App\Models\CartItem;
use App\Services\Discounts\Contracts\DiscountPriceCalculatorInterface;

abstract class DiscountPriceCalculator implements DiscountPriceCalculatorInterface
{
    protected abstract function calculatePrice(CartItem $item): float;
}
