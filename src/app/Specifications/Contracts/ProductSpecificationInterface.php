<?php


namespace App\Specifications\Contracts;


use App\Models\CartItem;

interface ProductSpecificationInterface
{
    public function isSatisfiedBy(CartItem $item): bool;
}
