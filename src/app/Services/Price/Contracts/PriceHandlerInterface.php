<?php


namespace App\Services\Price\Contracts;


use App\Models\CartItem;

interface PriceHandlerInterface
{
    public function handle(CartItem $item): CartItem;
}
