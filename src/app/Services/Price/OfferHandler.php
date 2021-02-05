<?php


namespace App\Services\Price;


use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Offer;
use App\Services\Price\Contracts\PriceHandlerInterface;
use App\Specifications\ApplicableForOfferSpecification;
use Illuminate\Support\Collection;

class OfferHandler implements PriceHandlerInterface
{
    protected Cart $cart;
    protected Collection $offers;
    protected ApplicableForOfferSpecification $applicableForOfferSpecification;
    protected ?CartItem $offeree = null;

    public function __construct(Cart $cart, Collection $offers, ApplicableForOfferSpecification $applicableForOfferSpecification)
    {
        $this->cart = $cart;
        $this->offers = $offers;
        $this->applicableForOfferSpecification = $applicableForOfferSpecification;
    }

    public function apply(CartItem $item): float
    {
        $totalOffersAmount = 0;
        foreach ($this->offers as $offer) {
            $this->applicableForOfferSpecification->setOffer($offer);
            if (!$this->applicableForOfferSpecification->isSatisfiedBy($item) ||
                !$this->cartHasOfferee($offer) ||
                !$this->cartIsValidForOffer($offer)) continue;

            $totalOffersAmount += $this->applyOffer($item, $offer);
        }

        return $totalOffersAmount;
    }

    protected function cartHasOfferee(Offer $offer): bool
    {
        return $this->cart->getItem($offer->getOfferee()) !== null;
    }

    protected function cartIsValidForOffer(Offer $offer): bool
    {
        $offeree = $this->cart->getItem($offer->getOfferee());
        if (!$offeree) return false;

        return $offeree->getQuantity() >= $offer->getQuantity();
    }

    protected function applyOffer(CartItem $item, Offer $offer): float
    {
        $discountPrice = $this->getDiscountPrice($item, $offer, $discountType);
        $item->setDiscountType($discountType);
        $item->setDiscount($offer->getDiscount()->getValue());

        return $discountPrice;
    }

    protected function getDiscountPrice(CartItem $item, Offer $offer, &$discountType = ''): float
    {
        switch ($offer->getDiscount()->getType()) {
            case Discount::PERCENTAGE:
                $discountType = Discount::PERCENTAGE;
                return $this->calculateByPercentage($item, $offer);
            case Discount::FIXED_PRICE:
                $discountType = Discount::FIXED_PRICE;
                return $this->calculateByFixedPrice($item, $offer);
            default:
                return 0;
        }
    }

    protected function calculateByPercentage(CartItem $item, Offer $offer): float
    {
        return ($this->getDiscountAmount($item, $offer) / 100) * $item->getOriginalPrice();
    }

    protected function calculateByFixedPrice(CartItem $item, Offer $offer): float
    {
        return $this->getDiscountAmount($item, $offer);
    }

    protected function getDiscountAmount(CartItem $item, Offer $offer): float
    {
        $applicableDiscount = intdiv($this->cart->getItem($offer->getOfferee())->getQuantity(), $offer->getQuantity());
        $discountCount = min($applicableDiscount, $item->getQuantity());
        return $discountCount  * $offer->getDiscount()->getValue();
    }
}
