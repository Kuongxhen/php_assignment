<?php

namespace App\Observers;

/**
 * Subject interface for the Observer pattern
 */
interface StockSubjectInterface
{
    public function attach(StockObserverInterface $observer): void;
    public function detach(StockObserverInterface $observer): void;
    public function notify(): void;
}
