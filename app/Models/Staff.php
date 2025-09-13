<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'staffId',
        'staffName',
        'staffEmail',
        'staffPhoneNumber',
        'dateHired',
        'role',
        'password',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'staffId');
    }

    public function getStaffIdAttribute()
    {
        return $this->attributes['staffId'] ?? null;
    }

    public function getStaffNameAttribute()
    {
        return $this->attributes['staffName'] ?? null;
    }

    public function getStaffEmailAttribute()
    {
        return $this->attributes['staffEmail'] ?? null;
    }

    public function getStaffPhoneNumberAttribute()
    {
        return $this->attributes['staffPhoneNumber'] ?? null;
    }

    public function getDateHiredAttribute()
    {
        return $this->attributes['dateHired'] ?? null;
    }

    public function getRoleAttribute()
    {
        return $this->attributes['role'] ?? null;
    }

    // Setters (redundant but provided for explicitness)
    public function setStaffIdAttribute($value): void
    {
        $this->attributes['staffId'] = $value;
    }

    public function setStaffNameAttribute($value): void
    {
        $this->attributes['staffName'] = $value;
    }

    public function setStaffEmailAttribute($value): void
    {
        $this->attributes['staffEmail'] = $value;
    }

    public function setStaffPhoneNumberAttribute($value): void
    {
        $this->attributes['staffPhoneNumber'] = $value;
    }

    public function setDateHiredAttribute($value): void
    {
        $this->attributes['dateHired'] = $value;
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['role'] = $value;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = $value;
    }
}


