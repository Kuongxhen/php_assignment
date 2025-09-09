<?php

namespace App\Decorators;

class DiscountDecorator extends PriceDecorator
{
    protected $discountRate = 0.05; // 5%

    public function getPrice(): float
    {
        return $this->price->getPrice() * (1 - $this->discountRate);
    }
}
