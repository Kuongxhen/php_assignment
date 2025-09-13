<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request as HttpRequest;

class DoctorController extends Controller
{
    public function dashboard(): View
    {
        // Check if user is doctor
        if (!session('user') || session('user_role') !== 'doctor') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as a doctor.');
        }
        
        // Get doctor's statistics
        $doctorId = session('user')->staffId;
        $todayAppointments = 12; // This would come from appointments table
        $availableSlots = 8; // This would be calculated
        $totalPatients = 45; // This would come from patients table
        $satisfactionRate = 95; // This would be calculated
        
        return view('doctor.dashboard', compact('todayAppointments', 'availableSlots', 'totalPatients', 'satisfactionRate'));
    }
    
    public function manageSchedule(): View
    {
        if (!session('user') || session('user_role') !== 'doctor') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as a doctor.');
        }
        $doctorId = session('user')->staffId;
        $leaves = \App\Models\DoctorLeave::where('staffId', $doctorId)->orderByDesc('start_date')->get();
        return view('doctor.leave', compact('leaves'));
    }
    
    
    public function store(Request $request)
    {
        if (!session('user') || session('user_role') !== 'doctor') {
            return redirect()->route('staffmod.login');
        }
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);
        \App\Models\DoctorLeave::create([
            'staffId' => session('user')->staffId,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'] ?? null,
        ]);
        return redirect()->route('staffmod.doctor.schedule')->with('success', 'Leave request submitted.');
    }
    
    /**
     * Delete a doctor's leave/schedule entry
     */
    public function deleteSchedule($id)
    {
        if (!session('user') || session('user_role') !== 'doctor') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as a doctor.');
        }
        
        try {
            $doctorId = session('user')->staffId;
            $leave = \App\Models\DoctorLeave::where('id', $id)
                ->where('staffId', $doctorId)
                ->firstOrFail();
            
            $leave->delete();
            
            return redirect()->route('staffmod.doctor.schedule')->with('success', 'Leave schedule deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete schedule: ' . $e->getMessage());
        }
    }
}


