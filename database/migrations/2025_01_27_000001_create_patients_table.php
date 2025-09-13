<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name');
            $table->string('ic_number')->unique(); // Identity Card/National ID
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->string('phone_number');
            $table->string('email')->unique()->nullable();
            $table->text('address');
            
            // Emergency Contact
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('emergency_contact_relationship');
            
            // Medical Information
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->text('current_medications')->nullable();
            $table->string('blood_type')->nullable();
            $table->text('chronic_conditions')->nullable();
            
            // System Fields
            $table->enum('status', ['active', 'inactive', 'deceased'])->default('active');
            $table->timestamp('last_visit')->nullable();
            $table->text('notes')->nullable(); // General notes by staff
            
            $table->timestamps();
            $table->softDeletes(); // For soft deletion of patient records
            
            // Indexes for better performance
            $table->index(['status', 'last_visit']);
            $table->index('ic_number');
            $table->unique('phone_number');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
