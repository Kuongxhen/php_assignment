<?php

namespace App\Models;

class Doctor extends Staff
{
    protected $table = 'doctors';

    protected $fillable = [
        'staffId',
        'staffName',
        'staffEmail',
        'staffPhoneNumber',
        'dateHired',
        'role',
        'password',
        'specialization'
    ];

    public function getSpecializationAttribute()
    {
        return $this->attributes['specialization'] ?? null;
    }

    public function setSpecializationAttribute($value): void
    {
        $this->attributes['specialization'] = $value;
    }
}


