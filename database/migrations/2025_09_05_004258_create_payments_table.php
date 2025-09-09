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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // BIGINT (PK) Primary Key
            $table->unsignedBigInteger('appointment_id'); // Link back to appointment
            $table->decimal('amount', 10, 2); // Final payment amount (consultation + medicine)
            $table->string('currency', 10)->default('MYR'); // Currency code (e.g., MYR, USD)
            $table->enum('method', ['credit_card', 'debit_card', 'bank_transfer', 'ewallet']); // Payment method
            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('pending'); // Payment status
            $table->string('transaction_reference', 100)->nullable(); // Unique transaction ID (from gateway or internal system)
            $table->timestamp('paid_at')->nullable(); // When the payment was completed
            $table->json('payment_details')->nullable(); // Store payment method specific details (encrypted)
            $table->json('gateway_response')->nullable(); // Store gateway response for debugging
            $table->text('failure_reason')->nullable(); // Store failure reason if payment fails
            $table->timestamps(); // created_at, updated_at

            // Foreign key constraint - Uncomment when appointments table is available
            // $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');

            // Indexes for better performance
            $table->index('appointment_id');
            $table->index('status');
            $table->index('method');
            $table->index('transaction_reference');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};