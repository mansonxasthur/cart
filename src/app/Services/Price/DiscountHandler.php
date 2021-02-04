<?php


namespace App\Services\Price;


use App\Models\CartItem;
use App\Models\Discount;
use App\Services\Discounts\Factories\Contracts\DiscountCalculatorFactoryInterface;
use App\Services\Price\Contracts\PriceHandlerInterface;
use App\Specifications\ProductHasDiscountSpecification;

class DiscountHandler implements PriceHandlerInterface
{
    protected ProductHasDiscountSpecification $discountSpecification;
    protected DiscountCalculatorFactoryInterface $discountFactory;

    public function __construct(ProductHasDiscountSpecification $discountSpecification, DiscountCalculatorFactoryInterface $discountFactory)
    {
        $this->discountSpecification = $discountSpecification;
        $this->discountFactory = $discountFactory;
    }

    public function handle(CartItem $item): CartItem
    {
        if (!$this->discountSpecification->isSatisfiedBy($item)) return $item;

        $discount = $this->makeDiscount($item->getDiscountType());
        $discount->apply($item);
        return $item;
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
