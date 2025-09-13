<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Doctor;
use App\Models\Receptionist;
use App\Models\Admin;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }
    
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'role' => 'required|in:doctor,receptionist,admin'
        ]);
        
        $credentials = $request->only('email', 'password');
        $role = $request->input('role');
        
        $attemptKey = 'login_attempts_' . $request->ip();
        $attempts = session($attemptKey, 0);
        if ($attempts >= 5) {
            \Log::warning("Too many login attempts from IP: " . $request->ip());
            return redirect()->back()->withErrors([
                'email' => 'Too many failed login attempts. Please try again later.'
            ])->withInput($request->except('password'));
        }
        
        $user = null; $userFound = false;
        try {
            switch ($role) {
                case 'doctor':
                    $user = Doctor::where('staffEmail', $credentials['email'])->first();
                    if ($user) { $userFound = $user->role === 'doctor'; }
                    break;
                case 'receptionist':
                    $user = Receptionist::where('staffEmail', $credentials['email'])->first();
                    if ($user) { $userFound = $user->role === 'receptionist'; }
                    break;
                case 'admin':
                    $user = Admin::where('staffEmail', $credentials['email'])->first();
                    if ($user) { $userFound = $user->role === 'admin'; }
                    break;
            }
            
            if ($user && $userFound && Hash::check($credentials['password'], $user->password)) {
                session()->forget($attemptKey);
                session(['user' => $user, 'user_role' => $role]);
                \Log::info("User {$user->staffName} ({$role}) logged in successfully from IP: " . $request->ip());
                switch ($role) {
                    case 'doctor':
                        return redirect()->route('staffmod.doctor.dashboard')->with('success', 'Welcome back, Dr. ' . $user->staffName . '!');
                    case 'receptionist':
                        return redirect()->route('staffmod.receptionist.dashboard')->with('success', 'Welcome back, ' . $user->staffName . '!');
                    case 'admin':
                        return redirect()->route('staffmod.admin.dashboard')->with('success', 'Welcome back, ' . $user->staffName . '!');
                }
            } else {
                session([$attemptKey => $attempts + 1]);
                \Log::warning("Failed login attempt for email: {$credentials['email']} with role: {$role} from IP: " . $request->ip() . " (Attempt: " . ($attempts + 1) . ")");
                $remainingAttempts = 5 - ($attempts + 1);
                $errorMessage = 'Invalid email, password, or role mismatch. Please check your credentials and try again.';
                if ($remainingAttempts > 0) { $errorMessage .= " ({$remainingAttempts} attempts remaining)"; }
                return redirect()->back()->withErrors(['email' => $errorMessage])->withInput($request->except('password'));
            }
        } catch (\Exception $e) {
            \Log::error("Database error during login: " . $e->getMessage());
            return redirect()->back()->withErrors(['email' => 'Login temporarily unavailable. Please try again later.'])->withInput($request->except('password'));
        }
    }
    
    public function logout(): RedirectResponse
    {
        session()->forget(['user', 'user_role']);
        return redirect('/')->with('success', 'Logged out successfully.');
    }
}

