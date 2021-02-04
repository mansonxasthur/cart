<?php


namespace App\Models;


class Currency
{
    const USD = 'USD';
    const EGP = 'EGP';
    const VALID_CURRENCIES = [
        self::USD,
        self::EGP,
    ];

    protected string $name;
    protected string $sign;
    protected float $exchange_rate;

    public function __construct(string $name, array $currencyInfo)
    {
        $this->name = $name;
        foreach ($currencyInfo as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function getExchangeRate(): float
    {
        return $this->exchange_rate;
    }
}
