<?php


namespace App\Services\Discounts\Factories;


use App\Services\Discounts\Factories\Contracts\DiscountCalculatorFactoryInterface;
use App\Services\Discounts\FixedPriceDiscountCalculator;
use App\Services\Discounts\PercentageDiscountCalculator;

class DiscountCalculatorFactory implements DiscountCalculatorFactoryInterface
{
    public function createPercentageDiscountCalculator(): PercentageDiscountCalculator
    {
        return new PercentageDiscountCalculator();
    }

    public function createFixedPriceDiscountCalculator(): FixedPriceDiscountCalculator
    {
        return new FixedPriceDiscountCalculator();
    }
}
