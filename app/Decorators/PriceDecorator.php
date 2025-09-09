<?php

namespace App\Decorators;

abstract class PriceDecorator implements PriceInterface
{
    protected $price;

    public function __construct(PriceInterface $price)
    {
        $this->price = $price;
    }

    abstract public function getPrice(): float;
}
