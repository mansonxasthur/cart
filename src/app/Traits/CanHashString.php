<?php


namespace App\Traits;


trait CanHashString
{
    public function hash(string $value): string
    {
        return md5($value);
    }
}
