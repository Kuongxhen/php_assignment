<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'alert_type', 
        'message', 
        'current_quantity', 
        'reorder_level', 
        'status', 
        'severity',
        'acknowledged_by',
        'acknowledged_at',
        'resolved_at'
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'current_quantity' => 'integer',
        'reorder_level' => 'integer'
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_ACKNOWLEDGED = 'acknowledged';
    const STATUS_RESOLVED = 'resolved';

    /**
     * Severity constants
     */
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Alert type constants
     */
    const TYPE_LOW_STOCK = 'low_stock';
    const TYPE_OUT_OF_STOCK = 'out_of_stock';
    const TYPE_EXPIRED = 'expired';

    /**
     * Get the product that this alert belongs to
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the user who acknowledged this alert
     */
    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Scope for active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for alerts by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Acknowledge this alert
     */
    public function acknowledge($userId = null)
    {
        $this->update([
            'status' => self::STATUS_ACKNOWLEDGED,
            'acknowledged_by' => $userId,
            'acknowledged_at' => now()
        ]);
    }

    /**
     * Resolve this alert
     */
    public function resolve()
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now()
        ]);
    }

    /**
     * Check if alert is overdue (active for more than 24 hours)
     */
    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->created_at->diffInHours(now()) > 24;
    }
}
