<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Providers\StaffService;
use App\Models\Doctor;
use App\Models\Receptionist;
use App\Models\Admin;
use App\Models\Product;
use App\Models\StockAlert;

class AdminController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    public function home(): View
    {
        return view('home');
    }

    public function showCreateStaffForm(): View|RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        return view('admin.create-staff');
    }

    public function createStaff(Request $request): JsonResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => 'Only administrators can create staff members.'
            ], 403);
        }

        try {
            $staff = $this->staffService->createStaff($request);

            return response()->json([
                'message' => 'Staff created successfully',
                'staff' => $staff
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating staff',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function dashboard(): View|RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $totalDoctors = Doctor::count();
        $totalReceptionists = Receptionist::count();
        $totalAdmins = Admin::count();
        $totalStaff = $totalDoctors + $totalReceptionists + $totalAdmins;

        return view('admin.dashboard', compact('totalStaff', 'totalDoctors', 'totalReceptionists', 'totalAdmins'));
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

    public function removeStaff($staffId)
    {
        if (!session('user') || session('user_role') !== 'admin') {
            abort(403, 'Only admin can remove staff.');
        }

        $deleted = false;
        if (Doctor::where('staffId', $staffId)->exists()) { Doctor::where('staffId', $staffId)->delete(); $deleted = true; }
        elseif (Receptionist::where('staffId', $staffId)->exists()) { Receptionist::where('staffId', $staffId)->delete(); $deleted = true; }
        elseif (Admin::where('staffId', $staffId)->exists()) { Admin::where('staffId', $staffId)->delete(); $deleted = true; }

        if (!$deleted) { abort(404, 'Staff not found.'); }

        return redirect()->back()->with('success', 'Staff removed successfully.');
    }
}