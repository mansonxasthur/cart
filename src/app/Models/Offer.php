<?php


namespace App\Models;


class Offer
{
    protected string $offeree;
    protected string $offerable;
    protected Discount $discount;
    protected int $quantity;

    /**
     * Offer constructor.
     *
     * @param string   $offeree   =====> product applied for offer
     * @param string   $offerable =====> product applicable for offer
     * @param Discount $discount
     * @param int      $quantity
     */
    public function __construct(string $offeree, string $offerable, Discount $discount, int $quantity)
    {
        $this->offeree = $offeree;
        $this->offerable = $offerable;
        $this->discount = $discount;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getOfferee(): string
    {
        return $this->offeree;
    }

    /**
     * @return string
     */
    public function getOfferable(): string
    {
        return $this->offerable;
    }

    /**
     * @return Discount
     */
    public function getDiscount(): Discount
    {
        return $this->discount;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
