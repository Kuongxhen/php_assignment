<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()->after('last_visit');
            $table->timestamp('visit_completed_at')->nullable()->after('checked_in_at');
            $table->text('medicine_prescribed')->nullable()->after('visit_completed_at');
            $table->text('doctor_notes')->nullable()->after('medicine_prescribed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'checked_in_at',
                'visit_completed_at', 
                'medicine_prescribed',
                'doctor_notes'
            ]);
        });
    }
};