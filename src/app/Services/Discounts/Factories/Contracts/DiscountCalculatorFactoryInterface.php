<?php


namespace App\Services\Discounts\Factories\Contracts;


use App\Services\Discounts\FixedPriceDiscountCalculator;
use App\Services\Discounts\PercentageDiscountCalculator;

interface DiscountCalculatorFactoryInterface
{
    public function createPercentageDiscountCalculator(): PercentageDiscountCalculator;
    public function createFixedPriceDiscountCalculator(): FixedPriceDiscountCalculator;
}
