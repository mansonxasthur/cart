<?php


namespace App\Specifications\Contracts;


interface CurrencySpecificationInterface
{
    public function isSatisfiedBy(string $currency): bool;
}
