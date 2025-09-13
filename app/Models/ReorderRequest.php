<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReorderRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'current_quantity',
        'reorder_level',
        'suggested_quantity',
        'priority',
        'status',
        'estimated_cost',
        'supplier',
        'notes',
        'requested_by',
        'approved_by',
        'approved_at',
        'expected_delivery',
    ];

    protected $casts = [
        'current_quantity' => 'integer',
        'reorder_level' => 'integer',
        'suggested_quantity' => 'integer',
        'estimated_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'expected_delivery' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ORDERED = 'ordered';
    const STATUS_RECEIVED = 'received';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Priority constants
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Request type constants
     */
    const TYPE_AUTOMATIC = 'automatic';
    const TYPE_MANUAL = 'manual';
    const TYPE_EMERGENCY = 'emergency';

    /**
     * Get the product for this reorder request
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the user who requested this reorder
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved this reorder
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for requests by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for urgent requests
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', self::PRIORITY_URGENT);
    }

    /**
     * Approve this reorder request
     */
    public function approve($userId, $actualQuantity = null, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
            'actual_quantity' => $actualQuantity ?? $this->suggested_quantity,
            'notes' => $notes ? $this->notes . "\n\nApproval notes: " . $notes : $this->notes
        ]);
    }

    /**
     * Reject this reorder request
     */
    public function reject($userId, $reason = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $userId,
            'approved_at' => now(),
            'notes' => $this->notes . "\n\nRejection reason: " . ($reason ?? 'No reason provided')
        ]);
    }

    /**
     * Mark as ordered
     */
    public function markAsOrdered($purchaseOrderNumber = null, $expectedDeliveryDate = null)
    {
        $this->update([
            'status' => self::STATUS_ORDERED,
            'ordered_at' => now(),
            'purchase_order_number' => $purchaseOrderNumber,
            'expected_delivery_date' => $expectedDeliveryDate
        ]);
    }

    /**
     * Mark as received and update product stock
     */
    public function markAsReceived($actualQuantity = null, $actualCost = null)
    {
        $receivedQuantity = $actualQuantity ?? $this->actual_quantity;
        
        $this->update([
            'status' => self::STATUS_RECEIVED,
            'received_at' => now(),
            'actual_quantity' => $receivedQuantity,
            'actual_cost' => $actualCost
        ]);

        // Update product stock
        if ($this->product) {
            $this->product->increment('quantity', $receivedQuantity);
        }
    }

    /**
     * Calculate estimated cost based on product cost and suggested quantity
     */
    public function getEstimatedCostAttribute($value)
    {
        // If estimated_cost is already set and not zero, return it
        if ($value && $value > 0) {
            return $value;
        }
        
        // Calculate based on product cost and suggested quantity
        if ($this->product && $this->suggested_quantity) {
            $unitCost = $this->product->cost ?? 0;
            return $unitCost * $this->suggested_quantity;
        }
        
        return $value ?? 0;
    }

    /**
     * Calculate total estimated cost
     */
    public function getTotalEstimatedCostAttribute()
    {
        return $this->estimated_cost;
    }

    /**
     * Calculate total actual cost
     */
    public function getTotalActualCostAttribute()
    {
        return $this->actual_cost ?? $this->estimated_cost;
    }

    /**
     * Check if request is overdue for approval
     */
    public function isOverdueForApproval(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $hours = match($this->priority) {
            self::PRIORITY_URGENT => 2,
            self::PRIORITY_HIGH => 8,
            self::PRIORITY_MEDIUM => 24,
            default => 72
        };

        return $this->created_at->diffInHours(now()) > $hours;
    }
}
