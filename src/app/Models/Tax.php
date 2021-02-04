<?php


namespace App\Models;


use App\Models\Contracts\TaxInterface;

class Tax implements TaxInterface
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function apply(float $price): float
    {
        $price += ($this->getValue() / 100) * $price;

        return $price;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
