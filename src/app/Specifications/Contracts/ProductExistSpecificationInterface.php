<?php


namespace App\Specifications\Contracts;


interface ProductExistSpecificationInterface
{
    public function isSatisfiedBy(string $productName): bool;
}
