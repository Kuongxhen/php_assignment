<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Safely drop existing single-column unique indexes if they exist
        $indexes = collect(DB::select('SHOW INDEX FROM `patients`'))
            ->pluck('Key_name')
            ->unique()
            ->all();

        $dropIfPresent = function (string $indexName) use ($indexes) {
            if (in_array($indexName, $indexes, true)) {
                DB::statement("ALTER TABLE `patients` DROP INDEX `$indexName`");
            }
        };

        $dropIfPresent('patients_ic_number_unique');
        $dropIfPresent('patients_email_unique');
        $dropIfPresent('patients_phone_number_unique');

        Schema::table('patients', function (Blueprint $table) {
            // Add composite unique indexes that ignore soft-deleted rows
            $table->unique(['ic_number', 'deleted_at'], 'patients_ic_number_deleted_unique');
            $table->unique(['email', 'deleted_at'], 'patients_email_deleted_unique');
            $table->unique(['phone_number', 'deleted_at'], 'patients_phone_deleted_unique');
        });
    }

    public function down(): void
    {
        // Safely drop composite indexes if present
        $indexes = collect(DB::select('SHOW INDEX FROM `patients`'))
            ->pluck('Key_name')
            ->unique()
            ->all();

        $dropIfPresent = function (string $indexName) use ($indexes) {
            if (in_array($indexName, $indexes, true)) {
                DB::statement("ALTER TABLE `patients` DROP INDEX `$indexName`");
            }
        };

        $dropIfPresent('patients_ic_number_deleted_unique');
        $dropIfPresent('patients_email_deleted_unique');
        $dropIfPresent('patients_phone_deleted_unique');

        Schema::table('patients', function (Blueprint $table) {
            // Restore original single-column unique indexes
            $table->unique('ic_number');
            $table->unique('email');
            $table->unique('phone_number');
        });
    }
};


