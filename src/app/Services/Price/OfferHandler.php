<?php


namespace App\Services\Price;


use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Offer;
use App\Services\Price\Contracts\PriceHandlerInterface;
use App\Specifications\ApplicableForOfferSpecification;

class OfferHandler implements PriceHandlerInterface
{
    protected Cart $cart;
    protected Offer $offer;
    protected ApplicableForOfferSpecification $applicableForOfferSpecification;
    protected ?CartItem $offeree = null;

    public function __construct(Cart $cart, Offer $offer, ApplicableForOfferSpecification $applicableForOfferSpecification)
    {

        $this->cart = $cart;
        $this->offer = $offer;
        $this->applicableForOfferSpecification = $applicableForOfferSpecification;
    }

    public function handle(CartItem $item): CartItem
    {
        if (!$this->applicableForOfferSpecification->isSatisfiedBy($item) ||
            !$this->cartHasOfferee() ||
            !$this->cartIsValidForOffer()) return $item;

        return $this->applyOffer($item);
    }

    protected function cartHasOfferee(): bool
    {
        if (!$this->offeree) {
            $this->offeree = $this->cart->getItem($this->offer->getOfferee());
        }
        return $this->offeree !== null;
    }

    protected function cartIsValidForOffer(): bool
    {
        if (!$this->offeree) {
            $this->offeree = $this->cart->getItem($this->offer->getOfferee());
        }
        return $this->offeree->getQuantity() >= $this->offer->getQuantity();
    }

    protected function applyOffer(CartItem $item): CartItem
    {

        $item->setTotalPrice(
            $this->getDiscountPrice($item, $discountType)
        );

        $item->setDiscountType($discountType);
        $item->setDiscount($this->offer->getDiscount()->getValue());
        return $item;
    }

    protected function getDiscountPrice(CartItem $item, &$discountType = ''): float
    {
        switch ($this->offer->getDiscount()->getType()) {
            case Discount::PERCENTAGE:
                $discountType = Discount::PERCENTAGE;
                return $this->calculateByPercentage($item);
            case Discount::FIXED_PRICE:
                $discountType = Discount::FIXED_PRICE;
                return $this->calculateByFixedPrice($item);
            default:
                return $item->getTotalPrice();
        }
    }

    protected function calculateByPercentage(CartItem $item): float
    {
        return $item->getTotalPrice() - (($this->getDiscountAmount($item) / 100) * $item->getOriginalPrice());
    }

    protected function calculateByFixedPrice(CartItem $item): float
    {
        return $item->getTotalPrice() - $this->getDiscountAmount($item);
    }

    protected function getDiscountAmount(CartItem $item): float
    {
        $applicableDiscount = intdiv($this->offeree->getQuantity(), $this->offer->getQuantity());
        $discountCount = min($applicableDiscount, $item->getQuantity());
        return $discountCount  * $this->offer->getDiscount()->getValue();
    }
}
