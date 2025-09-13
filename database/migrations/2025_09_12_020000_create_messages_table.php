<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->enum('category', ['general','prescription','billing'])->default('general');
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['sent','read','archived'])->default('sent');
            $table->timestamp('read_at')->nullable();
            // Simple reply (by receptionist)
            $table->text('reply_body')->nullable();
            $table->string('reply_by_name')->nullable();
            $table->timestamps();
            $table->index(['patient_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};


