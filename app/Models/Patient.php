<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Domain\Patient\State\ActiveState;
use App\Domain\Patient\State\DeceasedState;
use App\Domain\Patient\State\InactiveState;
use App\Domain\Patient\State\PatientState;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'ic_number',
        'gender',
        'date_of_birth',
        'phone_number',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'medical_history',
        'allergies',
        'current_medications',
        'blood_type',
        'chronic_conditions',
        'status',
        'last_visit',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_visit' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'date_of_birth',
        'last_visit',
        'deleted_at',
    ];

    // Relationships
    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }


    // Accessors
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->diffInYears(Carbon::now()) : null;
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getDaysSinceLastVisitAttribute()
    {
        return $this->last_visit ? $this->last_visit->diffInDays(Carbon::now()) : null;
    }

    public function getIsInactivePatientAttribute()
    {
        // Patient is considered inactive if no visit in 2+ years
        return $this->last_visit && $this->last_visit->diffInYears(Carbon::now()) >= 2;
    }

    public function isProfileComplete(): bool
    {
        $required = [
            $this->name,
            $this->ic_number,
            $this->gender,
            $this->date_of_birth,
            $this->phone_number,
            $this->address,
        ];

        foreach ($required as $value) {
            if (empty($value)) {
                return false;
            }
        }

        return true;
    }

    // --- State pattern integration ---
    public function getState(): PatientState
    {
        return match ($this->status) {
            'active' => new ActiveState(),
            'deceased' => new DeceasedState(),
            default => new InactiveState(),
        };
    }

    public function activate(?User $performedBy = null, ?string $notes = null): void
    {
        $this->getState()->activate($this, $performedBy, $notes);
    }

    public function deactivate(?User $performedBy = null, ?string $notes = null): void
    {
        $this->getState()->deactivate($this, $performedBy, $notes);
    }

    public function markDeceased(?User $performedBy = null, ?string $notes = null): void
    {
        $this->getState()->markDeceased($this, $performedBy, $notes);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeWithRecentVisit($query, $days = 30)
    {
        return $query->where('last_visit', '>=', Carbon::now()->subDays($days));
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByAgeRange($query, $minAge, $maxAge)
    {
        $maxDate = Carbon::now()->subYears($minAge);
        $minDate = Carbon::now()->subYears($maxAge + 1);
        
        return $query->whereBetween('date_of_birth', [$minDate, $maxDate]);
    }

    public function scopeWithAllergies($query)
    {
        return $query->whereNotNull('allergies')->where('allergies', '!=', '');
    }

    public function scopeWithChronicConditions($query)
    {
        return $query->whereNotNull('chronic_conditions')->where('chronic_conditions', '!=', '');
    }

    // Methods
    public function updateLastVisit()
    {
        $this->update(['last_visit' => Carbon::now()]);
    }

    public function hasAllergies()
    {
        return !empty($this->allergies);
    }

    public function hasChronicConditions()
    {
        return !empty($this->chronic_conditions);
    }

    // Patient status methods (simplified)
    
    /**
     * Get current active patient count (for statistics)
     */
    public static function getActiveQueueCount()
    {
        return static::where('status', 'active')->count();
    }
    
    /**
     * Simple queue status for patient dashboard
     */
    public function isInQueue()
    {
        return $this->status === 'active';
    }
    
    /**
     * Get simplified queue position
     */
    public function getQueuePosition()
    {
        if (!$this->isInQueue()) {
            return null;
        }
        
        return static::where('status', 'active')
                    ->where('created_at', '<', $this->created_at)
                    ->count() + 1;
    }

    public function isEligibleForDeletion()
    {
        // Can only delete if inactive for 2+ years or deceased
        return $this->status === 'deceased' || 
               ($this->last_visit && $this->last_visit->diffInYears(Carbon::now()) >= 2);
    }


    // Validation Rules
    public static function getValidationRules($patientId = null)
    {
        $icRule = 'required|string|max:20|unique:patients,ic_number';
        $emailRule = 'nullable|email|unique:patients,email';
        
        if ($patientId) {
            $icRule .= ',' . $patientId;
            $emailRule .= ',' . $patientId;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'ic_number' => [$icRule],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => [$emailRule],
            'address' => ['required', 'string'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'max:20'],
            'emergency_contact_relationship' => ['required', 'string', 'max:100'],
            'medical_history' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'chronic_conditions' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,inactive,deceased'],
            'notes' => ['nullable', 'string'],
        ];
    }

    // Search functionality
    public static function search($term)
    {
        return static::where('name', 'LIKE', "%{$term}%")
                    ->orWhere('ic_number', 'LIKE', "%{$term}%")
                    ->orWhere('phone_number', 'LIKE', "%{$term}%")
                    ->orWhere('email', 'LIKE', "%{$term}%");
    }

    // Statistics
    public static function getStatistics()
    {
        return [
            'total_patients' => static::count(),
            'active_patients' => static::active()->count(),
            'inactive_patients' => static::inactive()->count(),
            'patients_with_allergies' => static::withAllergies()->count(),
            'patients_with_chronic_conditions' => static::withChronicConditions()->count(),
            'recent_visits' => static::withRecentVisit(30)->count(),
            'by_gender' => [
                'male' => static::byGender('male')->count(),
                'female' => static::byGender('female')->count(),
                'other' => static::byGender('other')->count(),
            ],
            'by_age_group' => [
                'children' => static::byAgeRange(0, 17)->count(),
                'adults' => static::byAgeRange(18, 64)->count(),
                'seniors' => static::byAgeRange(65, 120)->count(),
            ]
        ];
    }
}


