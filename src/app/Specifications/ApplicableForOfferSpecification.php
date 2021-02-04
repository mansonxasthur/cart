<?php


namespace App\Specifications;


use App\Models\CartItem;
use App\Models\Offer;
use App\Specifications\Contracts\ProductSpecificationInterface;

class ApplicableForOfferSpecification implements ProductSpecificationInterface
{
    protected ?Offer $offer = null;

    public function isSatisfiedBy(CartItem $item): bool
    {
        if (!$this->offer) return false;
        return $item->getName() === $this->offer->getOfferable();
    }

    public function setOffer(Offer $offer): void
    {
        $this->offer = $offer;
    }
}
