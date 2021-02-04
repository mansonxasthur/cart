<?php


namespace App\Models;


class Product
{
    protected string $name;
    protected float $price;
    protected ?float $discount;
    protected ?string $discount_type;

    public function __construct(array $productData)
    {
        foreach ($productData as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function getDiscountType(): ?string
    {
        return $this->discount_type;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
