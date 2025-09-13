<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            return redirect()->route('patient.profile.create')->with('error', 'Create your patient profile first.');
        }
        $upcoming = Appointment::where('patient_id', $patient->id)
            ->whereDate('scheduled_at', '>=', now()->startOfDay())
            ->orderBy('scheduled_at')
            ->get();
        $past = Appointment::where('patient_id', $patient->id)
            ->whereDate('scheduled_at', '<', now()->startOfDay())
            ->orderByDesc('scheduled_at')
            ->get();
        return view('patient.appointments.index', compact('patient', 'upcoming', 'past'));
    }

    public function create(): View|RedirectResponse
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            return redirect()->route('patient.profile.create')->with('error', 'Create your patient profile first.');
        }
        return view('patient.appointments.create', compact('patient'));
    }

    public function store(Request $request): RedirectResponse
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            return redirect()->route('patient.profile.create')->with('error', 'Create your patient profile first.');
        }
        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'reason' => 'nullable|string|max:255',
            'symptoms' => 'nullable|string|max:500',
            'urgency' => 'nullable|in:normal,urgent,follow-up',
            'preferred_window' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $composedNotes = trim(collect([
            $validated['notes'] ?? null,
            isset($validated['symptoms']) ? 'Symptoms: ' . $validated['symptoms'] : null,
            isset($validated['urgency']) ? 'Urgency: ' . ucfirst($validated['urgency']) : null,
            isset($validated['preferred_window']) ? 'Preferred Window: ' . $validated['preferred_window'] : null,
        ])->filter()->implode("\n"));

        $payload = [
            'patient_id' => $patient->id,
            'scheduled_at' => $validated['scheduled_at'],
            'reason' => $validated['reason'] ?? null,
            'notes' => $composedNotes ?: null,
            'status' => 'pending',
        ];

        Appointment::create($payload);

        return redirect()->route('patient.appointments.index')->with('success', 'Appointment request submitted.');
    }
}
