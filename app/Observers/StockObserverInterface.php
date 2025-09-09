<?php

namespace App\Observers;

use App\Models\Product;

/**
 * Observer interface for stock monitoring
 */
interface StockObserverInterface
{
    public function update(Product $product): void;
}
