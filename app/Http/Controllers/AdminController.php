<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Receptionist;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function dashboard(): View|RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $totalDoctors = Doctor::count();
        $totalReceptionists = Receptionist::count();
        $totalAdmins = Admin::count();
        $activeReceptionists = Receptionist::where('status', 'active')->count();

        return view('admin.dashboard', compact('totalDoctors', 'totalReceptionists', 'totalAdmins', 'activeReceptionists'));
    }

    public function showCreateStaffForm(): View|RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        return view('admin.create-staff');
    }

    public function createStaff(Request $request): RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $request->validate([
            'role' => 'required|in:doctor,receptionist,admin',
            'staffName' => 'required|string|max:255',
            'staffEmail' => 'required|email|unique:doctors,staffEmail|unique:receptionists,staffEmail|unique:admins,staffEmail',
            'specialization' => 'nullable|string|max:255',
        ]);

        $staffId = 'STF' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $password = Hash::make('password123');

        switch($request->role) {
            case 'doctor':
                Doctor::create([
                    'staffId' => $staffId,
                    'staffName' => $request->staffName,
                    'staffEmail' => $request->staffEmail,
                    'password' => $password,
                    'specialization' => $request->specialization ?? 'General Medicine',
                ]);
                break;
            case 'receptionist':
                Receptionist::create([
                    'staffId' => $staffId,
                    'staffName' => $request->staffName,
                    'staffEmail' => $request->staffEmail,
                    'password' => $password,
                    'status' => 'active',
                ]);
                break;
            case 'admin':
                Admin::create([
                    'staffId' => $staffId,
                    'staffName' => $request->staffName,
                    'staffEmail' => $request->staffEmail,
                    'password' => $password,
                ]);
                break;
        }

        return redirect()->route('staffmod.admin.staffList')->with('success', 'Staff created successfully. Default password: password123');
    }

    public function listStaff(): View|RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $doctors = collect(\DB::table('doctors')->get())->map(function ($d) { $d->role = 'doctor'; $d->type = 'doctor'; return $d; });
        $receptionists = collect(\DB::table('receptionists')->get())->map(function ($r) { $r->role = 'receptionist'; $r->type = 'receptionist'; return $r; });
        $admins = collect(\DB::table('admins')->get())->map(function ($a) { $a->role = 'admin'; $a->type = 'admin'; return $a; });

        $staff = $doctors->merge($receptionists)->merge($admins)->values();

        return view('admin.staff-list', compact('staff'));
    }

    public function activateReceptionist($staffId): \Illuminate\Http\RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }
        $r = Receptionist::where('staffId', $staffId)->firstOrFail();
        $r->activate();
        return redirect()->back()->with('success', 'Receptionist activated.');
    }

    public function deactivateReceptionist($staffId): \Illuminate\Http\RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }
        $r = Receptionist::where('staffId', $staffId)->firstOrFail();
        $r->deactivate();
        return redirect()->back()->with('success', 'Receptionist deactivated.');
    }

    public function removeStaff($staffId): RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $deleted = false;
        if (Doctor::where('staffId', $staffId)->exists()) { Doctor::where('staffId', $staffId)->delete(); $deleted = true; }
        elseif (Receptionist::where('staffId', $staffId)->exists()) { Receptionist::where('staffId', $staffId)->delete(); $deleted = true; }
        elseif (Admin::where('staffId', $staffId)->exists()) { Admin::where('staffId', $staffId)->delete(); $deleted = true; }

        if (!$deleted) { abort(404, 'Staff not found.'); }

        return redirect()->back()->with('success', 'Staff removed successfully.');
    }
}
