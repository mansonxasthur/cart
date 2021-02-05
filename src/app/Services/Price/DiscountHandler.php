<?php


namespace App\Services\Price;


use App\Models\CartItem;
use App\Models\Discount;
use App\Services\Discounts\Factories\Contracts\DiscountCalculatorFactoryInterface;
use App\Services\Price\Contracts\PriceHandlerInterface;
use App\Specifications\Contracts\ProductSpecificationInterface;

class DiscountHandler implements PriceHandlerInterface
{
    protected ProductSpecificationInterface $discountSpecification;
    protected DiscountCalculatorFactoryInterface $discountFactory;

    public function __construct(ProductSpecificationInterface $discountSpecification, DiscountCalculatorFactoryInterface $discountFactory)
    {
        $this->discountSpecification = $discountSpecification;
        $this->discountFactory = $discountFactory;
    }

    public function apply(CartItem $item): float
    {
        if (!$this->discountSpecification->isSatisfiedBy($item)) return 0;

        $discount = $this->makeDiscount($item->getDiscountType());
        return $discount->apply($item);
    }

    protected function makeDiscount(?string $discountType)
    {
        switch ($discountType) {
            case Discount::PERCENTAGE:
                return $this->discountFactory->createPercentageDiscountCalculator();
            default:
                return $this->discountFactory->createFixedPriceDiscountCalculator();
        }
    }
}
