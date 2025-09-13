<?php

namespace App\Providers;

use App\Models\Doctor;
use App\Models\Receptionist;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class StaffService
{
    public function createStaff(Request $request)
    {
        $role = $request->role;

        $table = match($role) {
            'doctor' => 'doctors',
            'receptionist' => 'receptionists',
            'admin' => 'admins',
            default => 'doctors'
        };

        $validator = Validator::make($request->all(), [
            'staffId' => "required|unique:{$table},staffId",
            'staffName' => 'required|string|max:255',
            'staffEmail' => "required|email|unique:{$table},staffEmail",
            'staffPhoneNumber' => 'required|string',
            'dateHired' => 'required|date',
            'role' => 'required|in:doctor,receptionist,admin',
            'password' => 'required|min:6',
            'specialization' => $role === 'doctor' ? 'required' : 'sometimes'
        ]);

        $validator->after(function ($validator) use ($request) {
            $staffId = $request->input('staffId');
            if (!$staffId) { return; }

            $existsAnywhere = false;
            if (Schema::hasTable('doctors') && Doctor::where('staffId', $staffId)->exists()) { $existsAnywhere = true; }
            if (!$existsAnywhere && Schema::hasTable('receptionists') && Receptionist::where('staffId', $staffId)->exists()) { $existsAnywhere = true; }
            if (!$existsAnywhere && Schema::hasTable('admins') && Admin::where('staffId', $staffId)->exists()) { $existsAnywhere = true; }

            if ($existsAnywhere) {
                $validator->errors()->add('staffId', 'The staffId has already been taken by another staff member.');
            }
        });

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $validated = $validator->validated();

        switch ($validated['role']) {
            case 'doctor':
                return Doctor::create([
                    'staffId' => $validated['staffId'],
                    'staffName' => $validated['staffName'],
                    'staffEmail' => $validated['staffEmail'],
                    'staffPhoneNumber' => $validated['staffPhoneNumber'],
                    'dateHired' => $validated['dateHired'],
                    'role' => $validated['role'],
                    'password' => Hash::make($validated['password']),
                    'specialization' => $validated['specialization'] ?? null,
                ]);
            case 'receptionist':
                if (!Schema::hasTable('receptionists')) { throw new \Exception('Receptionist registration is not available yet'); }
                return Receptionist::create([
                    'staffId' => $validated['staffId'],
                    'staffName' => $validated['staffName'],
                    'staffEmail' => $validated['staffEmail'],
                    'staffPhoneNumber' => $validated['staffPhoneNumber'],
                    'dateHired' => $validated['dateHired'],
                    'role' => $validated['role'],
                    'password' => Hash::make($validated['password']),
                    'status' => 'active',
                ]);
            case 'admin':
                if (!Schema::hasTable('admins')) { throw new \Exception('Admin registration is not available yet'); }
                return Admin::create([
                    'staffId' => $validated['staffId'],
                    'staffName' => $validated['staffName'],
                    'staffEmail' => $validated['staffEmail'],
                    'staffPhoneNumber' => $validated['staffPhoneNumber'],
                    'dateHired' => $validated['dateHired'],
                    'role' => $validated['role'],
                    'password' => Hash::make($validated['password']),
                    'authorityLevel' => $request->adminLevel ?? 1,
                ]);
            default:
                throw new \Exception('Invalid role');
        }
    }
}


