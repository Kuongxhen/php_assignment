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
            // Remove the cost_price column
            $table->dropColumn('cost_price');
            
            // Add image_path column to store image URLs
            $table->string('image_path')->nullable()->after('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Re-add cost_price column
            $table->decimal('cost_price', 8, 2)->nullable()->after('supplier');
            
            // Remove image_path column
            $table->dropColumn('image_path');
        });
    }
};
