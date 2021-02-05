<?php


namespace App\Models;


class CartItem
{
    protected string $name;
    protected int $quantity;
    protected float $originalPrice;
    protected ?float $discount;
    protected ?string $discountType;
    protected float $totalPrice = 0;

    public function __construct(Product $product, int $quantity)
    {
        $this->quantity = $quantity;
        $this->name = $product->getName();
        $this->originalPrice = $product->getPrice();
        $this->discount = $product->getDiscount();
        $this->discountType = $product->getDiscountType();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getOriginalPrice(): float
    {
        return $this->originalPrice;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function getDiscountType(): ?string
    {
        return $this->discountType;
    }

    public function getTotalPrice(): float
    {
        if (!$this->totalPrice) {
            $this->totalPrice = $this->getOriginalPrice() * $this->getQuantity();
        }

        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    public function setDiscountType($discountType): void
    {
        $this->discountType = $discountType;
    }

    public function setDiscount(float $discount)
    {
        $this->discount = $discount;
    }
}
