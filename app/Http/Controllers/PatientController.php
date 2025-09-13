<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Display a listing of patients.
     */
    public function index(Request $request): View
    {
        $query = Patient::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ic_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(12)->withQueryString();
        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create(): View
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'required|string|max:20|unique:patients,ic_number',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'phone_number' => 'required|string|max:20|unique:patients,phone_number',
            'email' => 'nullable|email|max:255|unique:patients,email',
            'address' => 'required|string|max:500',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:100',
            'blood_type' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
        ], [
            // Custom error messages
            'name.required' => 'Patient name is required.',
            'ic_number.required' => 'IC Number is required.',
            'ic_number.unique' => 'This IC Number is already registered in our system.',
            'gender.required' => 'Please select a gender.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'phone_number.required' => 'Phone number is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered in our system.',
            'address.required' => 'Address is required.',
            'emergency_contact_name.required' => 'Emergency contact name is required.',
            'emergency_contact_phone.required' => 'Emergency contact phone is required.',
            'emergency_contact_relationship.required' => 'Emergency contact relationship is required.',
        ]);

        try {
            // Create the patient
            $patient = Patient::create($validated);

            // Redirect with success message
            return redirect()->route('patients.show', $patient)
                ->with('success', "Patient '{$patient->name}' has been successfully registered! (ID: {$patient->id})");

        } catch (\Exception $e) {
            // Handle any database errors
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create patient: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified patient.
     */
    public function show(Patient $patient): View
    {
        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:patients,phone_number,' . $patient->id,
            'email' => 'nullable|email|max:255|unique:patients,email,' . $patient->id,
            'address' => 'nullable|string|max:500',
        ]);

        try {
            $patient->update($validated);
            return redirect()->route('patients.show', $patient)->with('success', 'Patient updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update patient: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified patient from storage (soft delete).
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        try {
            $patient->delete();
            return redirect()->route('patients.index')->with('success', 'Patient removed.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete patient: ' . $e->getMessage()]);
        }
    }
}