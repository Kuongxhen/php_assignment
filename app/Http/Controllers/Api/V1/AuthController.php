<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'in:patient,staff,doctor,admin'],
                'phone_number' => ['nullable', 'string', 'max:20'],
                'gender' => ['nullable', 'in:male,female,other'],
                'date_of_birth' => ['nullable', 'date', 'before:today'],
                
                // Staff/Doctor specific fields
                'employee_id' => ['required_if:role,staff,doctor,admin', 'nullable', 'string', 'unique:users,employee_id'],
                'license_number' => ['required_if:role,doctor', 'nullable', 'string', 'max:50'],
                'specialization' => ['nullable', 'string', 'max:100'],
                'department' => ['nullable', 'string', 'max:100'],
                'hire_date' => ['nullable', 'date', 'before_or_equal:today'],
                
                // Patient specific fields (if registering as patient)
                'ic_number' => ['required_if:role,patient', 'nullable', 'string', 'max:20', 'unique:patients,ic_number'],
                'address' => ['required_if:role,patient', 'nullable', 'string'],
                'emergency_contact_name' => ['required_if:role,patient', 'nullable', 'string', 'max:255'],
                'emergency_contact_phone' => ['required_if:role,patient', 'nullable', 'string', 'max:20'],
                'emergency_contact_relationship' => ['required_if:role,patient', 'nullable', 'string', 'max:100'],
                'medical_history' => ['nullable', 'string'],
                'allergies' => ['nullable', 'string'],
                'current_medications' => ['nullable', 'string'],
                'blood_type' => ['nullable', 'string', 'max:10'],
                'chronic_conditions' => ['nullable', 'string'],
            ]);

            $validated['password'] = Hash::make($validated['password']);

            // Create patient record first if registering as patient
            $patientId = null;
            if ($validated['role'] === 'patient') {
                $patient = Patient::create([
                    'name' => $validated['name'],
                    'ic_number' => $validated['ic_number'],
                    'gender' => $validated['gender'] ?? 'other',
                    'date_of_birth' => $validated['date_of_birth'] ?? Carbon::now()->subYears(25),
                    'phone_number' => $validated['phone_number'] ?? '',
                    'email' => $validated['email'],
                    'address' => $validated['address'],
                    'emergency_contact_name' => $validated['emergency_contact_name'],
                    'emergency_contact_phone' => $validated['emergency_contact_phone'],
                    'emergency_contact_relationship' => $validated['emergency_contact_relationship'],
                    'medical_history' => $validated['medical_history'] ?? null,
                    'allergies' => $validated['allergies'] ?? null,
                    'current_medications' => $validated['current_medications'] ?? null,
                    'blood_type' => $validated['blood_type'] ?? null,
                    'chronic_conditions' => $validated['chronic_conditions'] ?? null,
                ]);
                $patientId = $patient->id;
            }

            // Create user
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => $validated['role'],
                'phone_number' => $validated['phone_number'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'patient_id' => $patientId,
            ];

            // Add staff/doctor specific fields
            if (in_array($validated['role'], ['staff', 'doctor', 'admin'])) {
                $userData['employee_id'] = $validated['employee_id'];
                $userData['department'] = $validated['department'] ?? null;
                $userData['hire_date'] = $validated['hire_date'] ?? Carbon::now();
            }

            if ($validated['role'] === 'doctor') {
                $userData['license_number'] = $validated['license_number'] ?? null;
                $userData['specialization'] = $validated['specialization'] ?? null;
            }

            $user = User::create($userData);

            // Generate token (if using Sanctum - you might want to install it)
            // $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user->load($user->isPatient() ? 'patient' : []),
                    // 'token' => $token, // Uncomment if using API tokens
                ]
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
                'message' => 'Registration failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
                'remember' => ['boolean']
            ]);

            if (!Auth::attempt([
                'email' => $validated['email'],
                'password' => $validated['password'],
                'status' => 'active' // Only allow active users to login
            ], $validated['remember'] ?? false)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials or account inactive'
                ], 401);
            }

            $user = Auth::user();
            $user->updateLastLogin();

            // Generate token (if using Sanctum)
            // $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => $user->load($user->isPatient() ? 'patient' : []),
                    // 'token' => $token, // Uncomment if using API tokens
                ]
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
                'message' => 'Login failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // If using Sanctum tokens
            // $request->user()->currentAccessToken()->delete();
            
            Auth::logout();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Profile retrieved successfully',
                'data' => $user->load($user->isPatient() ? 'patient' : [])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve profile',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
                'phone_number' => ['nullable', 'string', 'max:20'],
                'gender' => ['nullable', 'in:male,female,other'],
                'date_of_birth' => ['nullable', 'date', 'before:today'],
            ]);

            $user->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => $user->fresh()->load($user->isPatient() ? 'patient' : [])
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
                'message' => 'Failed to update profile',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = $request->user();

            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully'
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
                'message' => 'Failed to change password',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'password' => ['required', 'string'],
                'confirmation' => ['required', 'in:DELETE_MY_ACCOUNT']
            ]);

            $user = $request->user();

            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password is incorrect'
                ], 422);
            }

            // If user is a patient, also soft delete the patient record
            if ($user->isPatient() && $user->patient) {
                $user->patient->delete();
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Account deleted successfully'
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
                'message' => 'Failed to delete account',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user permissions/capabilities
     */
    public function permissions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $permissions = [
                'role' => $user->role,
                'can_manage_patients' => $user->canManagePatients(),
                'can_delete_patients' => $user->canDeletePatients(),
                'can_manage_appointments' => $user->canManageAppointments(),
                'can_view_all_appointments' => $user->canViewAllAppointments(),
                'can_manage_users' => $user->canManageUsers(),
                'can_access_reports' => $user->canAccessReports(),
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Permissions retrieved successfully',
                'data' => $permissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve permissions',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}

