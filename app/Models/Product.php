<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\StockSubject;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'description',
        'category',
        'sku',
        'price',
        'cost',
        'quantity',
        'reorder_level',
        'auto_reorder',
        'supplier',
        'cost_price',
        'unit',
        'manufacturer',
        'expiration_date',
        'is_active',
    ];

    protected $casts = [
        'auto_reorder' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'expiration_date' => 'date',
    ];

    // Relationships
    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class, 'product_id', 'product_id');
    }

    public function reorderRequests()
    {
        return $this->hasMany(ReorderRequest::class, 'product_id', 'product_id');
    }

    // Stock monitoring methods
    public function isLowStock()
    {
        return $this->quantity <= $this->reorder_level;
    }

    public function isOutOfStock()
    {
        return $this->quantity <= 0;
    }

    public function updateStock($newQuantity)
    {
        $oldQuantity = $this->quantity;
        $this->quantity = $newQuantity;
        $this->save();

        // Notify observers of stock change
        $stockSubject = new StockSubject();
        $stockSubject->notifyStockChange($this, $oldQuantity, $newQuantity);
    }
}
