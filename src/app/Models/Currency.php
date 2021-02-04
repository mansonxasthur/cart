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

    protected string $currency;
    protected string $sign;
    protected float $exchange_rate;

    public function __construct(string $currency, array $currencyInfo)
    {
        $this->currency = $currency;
        foreach ($currencyInfo as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            }
        }
    }

    public function getCurrency(): string
    {
        return $this->currency;
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
