<?php


namespace App\Models\Contracts;


interface TaxInterface
{
    public function collect(float $amount): float;
}
