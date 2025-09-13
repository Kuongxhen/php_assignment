<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
// Services removed; logic inlined in controller
// Removed patient type decorators
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

class ProfileController extends Controller
{
    public function __construct() {}
    
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please log in to access your profile.');
        }

        $patient = $user->patient;
        if ($patient) {
            $patient = $patient->fresh();
        }

        return view('patient.profile.show', compact('patient'));
    }

    public function edit(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please log in to manage your profile.');
        }

        $patient = $user->patient;
        if (!$patient) {
            return redirect()->route('patient.profile.create')->with('error', 'Create your patient profile first.');
        }
        return view('patient.profile.edit', compact('patient'));
    }

    public function create(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please log in before creating your profile.');
        }

        return view('patient.profile.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please log in before creating a profile.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'required|string|max:20|unique:patients,ic_number,NULL,id,deleted_at,NULL',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'phone_number' => 'required|string|max:20|unique:patients,phone_number,NULL,id,deleted_at,NULL',
            'email' => 'nullable|email|max:255|unique:patients,email,NULL,id,deleted_at,NULL',
            'address' => 'required|string|max:500',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:100',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:500',
            'medical_history' => 'nullable|string|max:1000',
            'current_medications' => 'nullable|string|max:500',
            'chronic_conditions' => 'nullable|string|max:500',
        ]);

        try {
            $patient = \App\Models\Patient::create(array_merge($validated, ['status' => 'inactive']));
            $user->update(['patient_id' => $patient->id]);
            return redirect()->route('patient.profile.show')->with('success', 'Profile created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create profile.');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please log in before deleting your profile.');
        }

        $patient = $user->patient;
        try {
            $patient->delete();
            Auth::user()->update(['patient_id' => null]);
            return redirect()->route('patient.profile.show')->with('success', 'Your profile has been deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete profile.');
        }
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please log in before updating your profile.');
        }

        $patient = $user->patient;
        if (!$patient) {
            return redirect()->route('patient.profile.create')->with('error', 'Create your patient profile first.');
        }
        
        // Basic Laravel validation (decorators will add additional validation)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:500',
            'current_medications' => 'nullable|string|max:500',
            'chronic_conditions' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        try {
            $patient->update($validated);

            // Auto-activate when profile becomes complete
            if ($patient->status !== 'deceased' && $patient->isProfileComplete() && $patient->status !== 'active') {
                $patient->activate(Auth::user(), 'Auto-activated upon completing profile');
            }

            if ($patient->user) {
                $patient->user->update([
                    'name' => $patient->name,
                    'email' => $patient->email ?: $patient->user->email,
                ]);
            }

            return redirect()->route('patient.profile.show')->with('success', 'Profile updated successfully.');

        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Update failed: ' . $e->getMessage());
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function deactivate(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please log in before changing your account status.');
        }

        $patient = $user->patient;
        try {
            $patient->deactivate(Auth::user(), 'Self-deactivated by patient');
            return redirect()->route('patient.profile.show')->with('success', 'Your account status is now inactive.');
        } catch (\DomainException $e) {
            return redirect()->route('patient.profile.show')->with('error', $e->getMessage());
        }
    }
}
