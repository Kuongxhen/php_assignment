<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('receptionists') && !Schema::hasColumn('receptionists','status')) {
            Schema::table('receptionists', function (Blueprint $table) {
                $table->enum('status', ['active','inactive'])->default('active')->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('receptionists') && Schema::hasColumn('receptionists','status')) {
            Schema::table('receptionists', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};


