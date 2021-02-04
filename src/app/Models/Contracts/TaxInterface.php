<?php


namespace App\Models\Contracts;


interface TaxInterface
{
    public function apply(float $price): float;
}
