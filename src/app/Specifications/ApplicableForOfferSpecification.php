<?php


namespace App\Specifications;


use App\Models\CartItem;
use App\Models\Offer;
use App\Specifications\Contracts\ProductSpecificationInterface;

class ApplicableForOfferSpecification implements ProductSpecificationInterface
{
    protected Offer $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function isSatisfiedBy(CartItem $item): bool
    {
        return $item->getName() === $this->offer->getOfferable();
    }
}
