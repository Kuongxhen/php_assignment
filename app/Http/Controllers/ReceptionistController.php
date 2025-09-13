<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use App\Models\Doctor;

class ReceptionistController extends Controller
{
    public function dashboard(): View
    {
        if (!session('user') || session('user_role') !== 'receptionist') {
            abort(403);
        }
        $totalDoctors = Doctor::count();
        $todayAppointments = 0;
        return view('receptionist.dashboard', compact('totalDoctors','todayAppointments'));
    }

    public function appointmentManagement(): View
    {
        if (!session('user') || session('user_role') !== 'receptionist') {
            abort(403);
        }
        return view('receptionist.appointments');
    }
}


