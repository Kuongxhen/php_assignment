<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
// Services removed; logic inlined in controller
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Illuminate\Validation\ValidationException;

class PatientAuthController extends Controller
{
    public function __construct() {}
    
    /**
     * Show patient registration form
     */
    public function showRegisterForm(): View
    {
        return view('auth.patient-register');
    }
    
    /**
     * Handle patient registration
     */
    public function register(Request $request): RedirectResponse
    {
        // Mode A: Minimal sign-up (name, email, password)
        if ($request->has('password')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'patient',
                    'status' => 'active',
                ]);

                Auth::login($user);

                return redirect()->route('patient.dashboard')->with('success', 'Welcome, ' . $user->name . '! Your account has been created.');

            } catch (InvalidArgumentException $e) {
                return redirect()->back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
            }
        }

        // Mode B: Full sign-up (existing flow)
        $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'required|string|unique:patients,ic_number',
            'phone_number' => 'required|string|unique:patients,phone_number',
            'email' => 'nullable|email|unique:patients,email',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'medical_history' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:500',
            'current_medications' => 'nullable|string|max:500',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'chronic_conditions' => 'nullable|string|max:500',
        ]);

        try {
            $patientData = array_map(function($value) {
                return $value === '' ? null : $value;
            }, $request->all());

            $patient = Patient::create(array_merge($patientData, [
                'status' => 'inactive',
            ]));

            $user = User::create([
                'name' => $patient->name,
                'email' => $patient->email ?: ($patient->ic_number . '@clinic.local'),
                'password' => Hash::make($patient->phone_number),
                'role' => 'patient',
                'status' => 'active',
                'patient_id' => $patient->id,
            ]);

            Auth::login($user);

            return redirect()->route('patient.dashboard')->with('success', 'Registration successful! Welcome to our clinic, ' . $patient->name . '.');

        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Handle patient login
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        
        try {
            // Try to find user by email first
            $user = User::where('email', $request->email)->where('role', 'patient')->first();
            
            if (!$user) {
                // If no user found by email, try to find patient by email and create user account
                $patient = Patient::where('email', $request->email)->first();
                
                if (!$patient) {
                    throw ValidationException::withMessages([
                        'email' => 'No patient account found with this email address.',
                    ]);
                }
                
                // Create user account if patient exists but no user account
                $user = User::create([
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'password' => Hash::make($patient->phone_number), // Default password is phone number
                    'role' => 'patient',
                    'status' => 'active',
                    'patient_id' => $patient->id,
                ]);
                
                $patient->update(['user_id' => $user->id]);
            }
            
            // Attempt authentication with email and password
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'patient',
                'status' => 'active'
            ];
            
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                
                return redirect()->route('patient.dashboard')->with('success', 'Welcome back!');
            }
            
            throw ValidationException::withMessages([
                'password' => 'The provided credentials do not match our records.',
            ]);
            
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput($request->only('email'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Login failed: ' . $e->getMessage()])->withInput($request->only('email'));
        }
    }
    
    /**
     * Show patient dashboard
     */
    public function dashboard(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', 'Please sign in to access your dashboard.');
        }

        $user = Auth::user();
        $patient = $user->patient; // IMPORTANT: do not auto-create a patient
        
        return view('patient.dashboard', compact('patient'));
    }
    
    /**
     * Handle patient logout
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}