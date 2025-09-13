<?php

namespace App\Models;

class Receptionist extends Staff
{
    protected $table = 'receptionists';

    protected $fillable = [
        'staffId',
        'staffName',
        'staffEmail',
        'staffPhoneNumber',
        'dateHired',
        'role',
        'password',
        'status',
        'shift'
    ];

    public function getShiftAttribute()
    {
        return $this->attributes['shift'] ?? null;
    }

    public function setShiftAttribute($value): void
    {
        $this->attributes['shift'] = $value;
    }

    // State pattern integration
    public function getState(): \App\Domain\Receptionist\State\ReceptionistState
    {
        return match ($this->status) {
            'active' => new \App\Domain\Receptionist\State\ActiveState(),
            default => new \App\Domain\Receptionist\State\InactiveState(),
        };
    }

    public function activate(?User $by = null, ?string $notes = null): void
    {
        $this->getState()->activate($this, $by, $notes);
    }

    public function deactivate(?User $by = null, ?string $notes = null): void
    {
        $this->getState()->deactivate($this, $by, $notes);
    }
}


