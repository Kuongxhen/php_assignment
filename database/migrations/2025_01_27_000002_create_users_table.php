<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Basic Authentication Fields
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Role-based Access Control
            $table->enum('role', ['patient', 'staff', 'doctor', 'admin'])->default('patient');
            
            // Profile Information
            $table->string('phone_number')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            
            // Staff/Doctor Specific Fields
            $table->string('employee_id')->unique()->nullable(); // For staff/doctors
            $table->string('license_number')->nullable(); // For doctors
            $table->string('specialization')->nullable(); // For doctors
            $table->string('department')->nullable(); // For staff/doctors
            $table->date('hire_date')->nullable(); // For staff/doctors
            
            // Patient Link (if user is a patient)
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            
            // System Fields
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // For soft deletion of user accounts
            
            // Indexes
            $table->index(['role', 'status']);
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
