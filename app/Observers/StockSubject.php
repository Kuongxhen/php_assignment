<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\StockAlert;

/**
 * Concrete Subject for stock monitoring
 * Implements the Observer pattern to notify when stock levels change
 */
class StockSubject implements StockSubjectInterface
{
    /**
     * @var StockObserverInterface[]
     */
    private array $observers = [];
    
    private Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Attach an observer to the subject
     */
    public function attach(StockObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    /**
     * Detach an observer from the subject
     */
    public function detach(StockObserverInterface $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn($obs) => $obs !== $observer
        );
    }

    /**
     * Notify all observers when stock changes
     */
    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this->product);
        }
    }

    /**
     * Get the product being monitored
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * Update product and notify observers
     */
    public function updateStock(int $newQuantity): void
    {
        $oldQuantity = $this->product->quantity;
        $this->product->quantity = $newQuantity;
        $this->product->save();

        // Only notify if stock decreased or reached reorder level
        if ($newQuantity < $oldQuantity || $this->isLowStock()) {
            $this->notify();
        }
    }

    /**
     * Check if stock is below reorder level
     */
    public function isLowStock(): bool
    {
        return $this->product->quantity <= ($this->product->reorder_level ?? 10);
    }
}
