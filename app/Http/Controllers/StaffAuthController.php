<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    /**
     * Handle staff login
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        try {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');
            
            // Find user by email
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => 'No staff account found with this email.',
                ]);
            }
            
            // Check if user is staff
            if (!in_array($user->role, ['admin', 'doctor', 'nurse', 'receptionist'])) {
                throw ValidationException::withMessages([
                    'email' => 'This account is not authorized for staff access.',
                ]);
            }
            
            // Check if user is active
            if ($user->status !== 'active') {
                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Contact administrator.',
                ]);
            }
            
            // Attempt authentication
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                return redirect()->route('staff.dashboard')->with('success', 
                    'Welcome back, ' . $user->name . '! (' . ucfirst($user->role) . ')');
            }
            
            throw ValidationException::withMessages([
                'password' => 'The provided password is incorrect.',
            ]);
            
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput($request->only('email', 'remember'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Login failed: ' . $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Show staff dashboard
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        
        // Ensure user is staff
        if (!in_array($user->role, ['admin', 'doctor', 'nurse', 'receptionist'])) {
            abort(403, 'Unauthorized access');
        }
        
        return view('staff.dashboard', compact('user'));
    }
    
    /**
     * Handle staff logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $userName = Auth::user()->name;
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Goodbye ' . $userName . '! You have been logged out successfully.');
    }
    
    /**
     * Create default admin user (for demo purposes)
     */
    public function createDefaultAdmin(): RedirectResponse
    {
        try {
            // Check if admin already exists
            $adminExists = User::where('role', 'admin')->exists();
            
            if ($adminExists) {
                return redirect()->back()->with('error', 'Admin user already exists.');
            }
            
            // Create default admin
            $admin = User::create([
                'name' => 'System Administrator',
                'email' => 'admin@clinic.local',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]);
            
            return redirect()->back()->with('success', 
                'Default admin created! Email: admin@clinic.local, Password: admin123');
                
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create admin: ' . $e->getMessage()]);
        }
    }
}