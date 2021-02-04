<?php


namespace App\Models;


class Discount
{
    const PERCENTAGE = 'percentage';
    const FIXED_PRICE = 'fixed_price';
    const TYPES = [
        self::PERCENTAGE,
        self::FIXED_PRICE,
    ];

    protected float $value;
    protected string $type;

    public function __construct(float $value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
