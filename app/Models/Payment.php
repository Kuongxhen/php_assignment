<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'amount',
        'currency',
        'method',
        'status',
        'transaction_reference',
        'paid_at',
        'payment_details',
        'gateway_response',
        'failure_reason',
    ];

    /**
     * Get the appointment that owns the payment
     */
    public function appointment()
    {
        // Note: This assumes the appointment model exists in the appointment module
        // For now, we'll use a generic relationship that can be customized
        return $this->belongsTo('App\Models\Appointment', 'appointment_id');
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_details' => 'array',
        'gateway_response' => 'array',
    ];

    /**
     * Payment status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Payment method constants
     */
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_DEBIT_CARD = 'debit_card';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_EWALLET = 'ewallet';

    /**
     * Scope for successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESSFUL);
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for refunded payments
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }

    /**
     * Scope for specific payment method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Scope for specific appointment
     */
    public function scopeForAppointment($query, $appointmentId)
    {
        return $query->where('appointment_id', $appointmentId);
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESSFUL;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if payment is refunded
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Mark payment as successful
     */
    public function markAsSuccessful(string $transactionReference = null): bool
    {
        return $this->update([
            'status' => self::STATUS_SUCCESSFUL,
            'transaction_reference' => $transactionReference,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $failureReason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'failure_reason' => $failureReason,
        ]);
    }

    /**
     * Mark payment as refunded
     */
    public function markAsRefunded(): bool
    {
        return $this->update([
            'status' => self::STATUS_REFUNDED,
        ]);
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get payment method display name
     */
    public function getMethodDisplayNameAttribute(): string
    {
        return match($this->method) {
            self::METHOD_CREDIT_CARD => 'Credit Card',
            self::METHOD_DEBIT_CARD => 'Debit Card',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_EWALLET => 'E-Wallet',
            default => ucfirst(str_replace('_', ' ', $this->method))
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayNameAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUCCESSFUL => 'Successful',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_REFUNDED => 'Refunded',
            default => ucfirst($this->status)
        };
    }
}
