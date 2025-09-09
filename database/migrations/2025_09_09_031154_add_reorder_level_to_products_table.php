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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('auto_reorder')->default(true)->after('reorder_level');
            $table->string('supplier')->nullable()->after('auto_reorder');
            $table->decimal('cost_price', 8, 2)->nullable()->after('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['auto_reorder', 'supplier', 'cost_price']);
        });
    }
};
