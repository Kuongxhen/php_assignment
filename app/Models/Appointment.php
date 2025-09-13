<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'scheduled_at',
        'reason',
        'notes',
        'status', // pending, confirmed, completed, cancelled
        'doctor_name',
        'location'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function isUpcoming(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture() && $this->status !== 'cancelled';
    }

    public function isPast(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isPast();
    }
}
