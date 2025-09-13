<?php

namespace App\Models;

class Admin extends Staff
{
    protected $table = 'admins';

    protected $fillable = [
        'staffId',
        'staffName',
        'staffEmail',
        'staffPhoneNumber',
        'dateHired',
        'role',
        'password',
        'authorityLevel'
    ];

    public function getAuthorityLevelAttribute()
    {
        return $this->attributes['authorityLevel'] ?? null;
    }

    public function setAuthorityLevelAttribute($value): void
    {
        $this->attributes['authorityLevel'] = $value;
    }
}


