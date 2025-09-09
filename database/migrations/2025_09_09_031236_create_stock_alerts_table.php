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
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->enum('alert_type', ['low_stock', 'out_of_stock', 'expired'])->default('low_stock');
            $table->text('message');
            $table->integer('current_quantity');
            $table->integer('reorder_level');
            $table->enum('status', ['active', 'acknowledged', 'resolved'])->default('active');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['severity', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
    }
};
