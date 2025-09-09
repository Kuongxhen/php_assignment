<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');  
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('category', 100); // Medication, Supplement, Equipment
            $table->string('sku', 50)->unique(); // Stock keeping unit
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2); // For profit tracking
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(5); // Alerts
            $table->string('unit', 50)->default('pcs'); // tablets, box, bottle
            $table->string('manufacturer', 255)->nullable();
            $table->date('expiration_date')->nullable(); // For meds
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
