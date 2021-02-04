<?php


namespace App\Specifications;


use App\Models\Currency;
use App\Specifications\Contracts\CurrencySpecificationInterface;

class ValidCurrencySpecification implements CurrencySpecificationInterface
{
    public function isSatisfiedBy(string $currency): bool
    {
        return in_array($currency, Currency::VALID_CURRENCIES);
    }
}
