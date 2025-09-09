<?php

namespace App\Decorators;

class TaxDecorator extends PriceDecorator
{
    protected $taxRate = 0.06; // 6%

    public function getPrice(): float
    {
        return $this->price->getPrice() * (1 + $this->taxRate);
    }
}

