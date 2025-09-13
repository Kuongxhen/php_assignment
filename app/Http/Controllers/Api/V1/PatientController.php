<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check permissions
            if (!auth()->user()->canManagePatients()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to view patients'
                ], 403);
            }

            $query = Patient::query();
            
            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('gender')) {
                $query->where('gender', $request->gender);
            }
            
            if ($request->has('has_allergies') && $request->has_allergies) {
                $query->withAllergies();
            }
            
            if ($request->has('has_chronic_conditions') && $request->has_chronic_conditions) {
                $query->withChronicConditions();
            }
            
            if ($request->has('age_min') && $request->has('age_max')) {
                $query->byAgeRange($request->age_min, $request->age_max);
            }
            
            if ($request->has('recent_visit_days')) {
                $query->withRecentVisit($request->recent_visit_days);
            }
            
            // Search functionality
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('ic_number', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('phone_number', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $patients = $query->latest()->paginate($perPage);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Patients retrieved successfully',
                'data' => $patients->items(),
                'meta' => [
                    'total' => $patients->total(),
                    'current_page' => $patients->currentPage(),
                    'per_page' => $patients->perPage(),
                    'last_page' => $patients->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve patients',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check permissions
            if (!auth()->user()->canManagePatients()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to create patients'
                ], 403);
            }

            $validated = $request->validate(Patient::getValidationRules());
            
            // Check for duplicate IC number
            if (Patient::where('ic_number', $validated['ic_number'])->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Patient with this IC number already exists'
                ], 422);
            }
            
            $patient = Patient::create($validated);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Patient created successfully',
                'data' => $patient
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create patient',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient): JsonResponse
    {
        try {
            // Check permissions - patients can only view their own record
            $user = auth()->user();
            if ($user->isPatient() && $user->patient_id !== $patient->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to view this patient record'
                ], 403);
            } elseif (!$user->isPatient() && !$user->canManagePatients()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to view patient records'
                ], 403);
            }

            // Load relationships based on user role
            $relationships = ['user'];
            if ($user->canManagePatients()) {
                $relationships = array_merge($relationships, ['appointments', 'consultations', 'prescriptions']);
            }
            
            $patient->load($relationships);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Patient retrieved successfully',
                'data' => $patient
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve patient',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient): JsonResponse
    {
        try {
            // Check permissions - patients can update limited fields of their own record
            $user = auth()->user();
            if ($user->isPatient() && $user->patient_id !== $patient->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update this patient record'
                ], 403);
            } elseif (!$user->isPatient() && !$user->canManagePatients()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update patient records'
                ], 403);
            }

            // Limit fields that patients can update themselves
            $validationRules = Patient::getValidationRules($patient->id);
            if ($user->isPatient()) {
                $allowedFields = [
                    'phone_number', 'email', 'address', 'emergency_contact_name',
                    'emergency_contact_phone', 'emergency_contact_relationship',
                    'current_medications', 'allergies'
                ];
                $validationRules = array_intersect_key($validationRules, array_flip($allowedFields));
            }
            
            $validated = $request->validate($validationRules);
            
            // Update last visit if this is a medical staff update
            if ($user->canManagePatients() && !$request->has('last_visit')) {
                $validated['last_visit'] = Carbon::now();
            }
            
            $patient->update($validated);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Patient updated successfully',
                'data' => $patient->fresh()
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update patient',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified patient (soft delete)
     */
    public function destroy(Patient $patient): JsonResponse
    {
        try {
            // Only admins can delete patients
            if (!auth()->user()->canDeletePatients()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to delete patients'
                ], 403);
            }

            // Check if patient is eligible for deletion
            if (!$patient->isEligibleForDeletion()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Patient cannot be deleted. Must be inactive for 2+ years or deceased.'
                ], 422);
            }
            
            $patient->delete(); // Soft delete
            
            return response()->json([
                'status' => 'success',
                'message' => 'Patient deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete patient',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Search patients
     */
    public function search(Request $request): JsonResponse
    {
        try {
            if (!auth()->user()->canManagePatients()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to search patients'
                ], 403);
            }

            $request->validate([
                'query' => ['required', 'string', 'min:2']
            ]);

            $patients = Patient::search($request->query)
                             ->active()
                             ->limit(20)
                             ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Search completed successfully',
                'data' => $patients
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get patient statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            if (!auth()->user()->canAccessReports()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to view statistics'
                ], 403);
            }

            $stats = Patient::getStatistics();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update patient status (active/inactive/deceased)
     */
    public function updateStatus(Request $request, Patient $patient): JsonResponse
    {
        try {
            if (!auth()->user()->canManagePatients()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update patient status'
                ], 403);
            }

            $validated = $request->validate([
                'status' => ['required', 'in:active,inactive,deceased'],
                'notes' => ['nullable', 'string', 'max:1000']
            ]);

            $user = auth()->user();
            switch ($validated['status']) {
                case 'active':
                    $patient->activate($user, $validated['notes'] ?? null);
                    break;
                case 'inactive':
                    $patient->deactivate($user, $validated['notes'] ?? null);
                    break;
                case 'deceased':
                    $patient->markDeceased($user, $validated['notes'] ?? null);
                    break;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Patient status updated successfully',
                'data' => $patient->fresh()
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update patient status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get patients eligible for deletion (inactive 2+ years)
     */
    public function eligibleForDeletion(): JsonResponse
    {
        try {
            if (!auth()->user()->canDeletePatients()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to view deletion candidates'
                ], 403);
            }

            $cutoffDate = Carbon::now()->subYears(2);
            $patients = Patient::where(function($query) use ($cutoffDate) {
                $query->where('status', 'deceased')
                      ->orWhere('last_visit', '<', $cutoffDate);
            })->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Deletion candidates retrieved successfully',
                'data' => $patients
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve deletion candidates',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}


