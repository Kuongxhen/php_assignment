<?php

namespace App\Decorators;

class BasePrice implements PriceInterface
{
    protected $price;

    public function __construct(float $price)
    {
        $this->price = $price;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
