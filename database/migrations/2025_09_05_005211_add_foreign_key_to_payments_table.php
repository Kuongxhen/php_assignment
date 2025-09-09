<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTE: This migration should only be run AFTER the appointments table has been created
     * in the appointment module. Uncomment the code below when ready.
     */
    public function up(): void
    {
        // Uncomment the following code when the appointments table is available:
        
        // Schema::table('payments', function (Blueprint $table) {
        //     // Add foreign key constraint to appointments table
        //     $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
        // });
        
        // For now, we'll just add a comment to indicate this migration is ready
        Schema::table('payments', function (Blueprint $table) {
            // This is a placeholder - the actual foreign key will be added when appointments table exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Uncomment the following code when the appointments table is available:
        
        // Schema::table('payments', function (Blueprint $table) {
        //     $table->dropForeign(['appointment_id']);
        // });
    }
};