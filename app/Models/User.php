<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'gender',
        'date_of_birth',
        'employee_id',
        'license_number',
        'specialization',
        'department',
        'hire_date',
        'patient_id',
        'status',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'last_login' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id')->where('role', 'doctor');
    }

    // Role-based methods
    public function isPatient()
    {
        return $this->role === 'patient';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole($roles)
    {
        return in_array($this->role, $roles);
    }

    // Permission methods
    public function canManagePatients()
    {
        return in_array($this->role, ['staff', 'doctor', 'admin']);
    }

    public function canDeletePatients()
    {
        return $this->role === 'admin';
    }

    public function canManageAppointments()
    {
        return in_array($this->role, ['staff', 'doctor', 'admin']);
    }

    public function canViewAllAppointments()
    {
        return in_array($this->role, ['staff', 'admin']);
    }

    public function canManageUsers()
    {
        return $this->role === 'admin';
    }

    public function canAccessReports()
    {
        return in_array($this->role, ['doctor', 'admin']);
    }

    // Scopes
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeStaff($query)
    {
        return $query->whereIn('role', ['staff', 'doctor', 'admin']);
    }

    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    public function scopePatients($query)
    {
        return $query->where('role', 'patient');
    }

    // Methods
    public function updateLastLogin()
    {
        $this->update(['last_login' => Carbon::now()]);
    }

    public function getDisplayNameAttribute()
    {
        if ($this->isDoctor() && $this->specialization) {
            return "Dr. {$this->name} ({$this->specialization})";
        }
        
        return $this->name;
    }

    // Validation Rules
    public static function getValidationRules($userId = null, $role = 'patient')
    {
        $emailRule = 'required|email|unique:users,email';
        $employeeIdRule = 'nullable|string|unique:users,employee_id';
        
        if ($userId) {
            $emailRule .= ',' . $userId;
            $employeeIdRule .= ',' . $userId;
        }

        $baseRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [$emailRule],
            'password' => $userId ? ['sometimes', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'role' => ['required', 'in:patient,staff,doctor,admin'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'status' => ['sometimes', 'in:active,inactive,suspended'],
        ];

        // Add role-specific rules
        if (in_array($role, ['staff', 'doctor', 'admin'])) {
            $baseRules['employee_id'] = [$employeeIdRule];
            $baseRules['department'] = ['nullable', 'string', 'max:100'];
            $baseRules['hire_date'] = ['nullable', 'date', 'before_or_equal:today'];
        }

        if ($role === 'doctor') {
            $baseRules['license_number'] = ['nullable', 'string', 'max:50'];
            $baseRules['specialization'] = ['nullable', 'string', 'max:100'];
        }

        if ($role === 'patient') {
            $baseRules['patient_id'] = ['nullable', 'exists:patients,id'];
        }

        return $baseRules;
    }

    // Statistics
    public static function getStatistics()
    {
        return [
            'total_users' => static::count(),
            'active_users' => static::active()->count(),
            'by_role' => [
                'patients' => static::byRole('patient')->count(),
                'staff' => static::byRole('staff')->count(),
                'doctors' => static::byRole('doctor')->count(),
                'admins' => static::byRole('admin')->count(),
            ],
            'recent_logins' => static::where('last_login', '>=', Carbon::now()->subDays(7))->count(),
        ];
    }
}
