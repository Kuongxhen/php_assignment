<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id('staffId');
                $table->string('staffName');
                $table->string('staffEmail')->unique();
                $table->string('staffPhoneNumber');
                $table->date('dateHired');
                $table->enum('role', ['doctor', 'receptionist', 'admin']);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('doctors')) {
            Schema::create('doctors', function (Blueprint $table) {
                $table->id('staffId');
                $table->string('staffName');
                $table->string('staffEmail')->unique();
                $table->string('staffPhoneNumber');
                $table->date('dateHired');
                $table->string('password');
                $table->string('role')->default('doctor');
                $table->string('specialization')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('receptionists')) {
            Schema::create('receptionists', function (Blueprint $table) {
                $table->id('staffId');
                $table->string('staffName');
                $table->string('staffEmail')->unique();
                $table->string('staffPhoneNumber');
                $table->date('dateHired');
                $table->string('role')->default('receptionist');
                $table->string('password');
                $table->string('shift')->nullable();
                $table->string('deskNumber')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id('staffId');
                $table->string('staffName');
                $table->string('staffEmail')->unique();
                $table->string('staffPhoneNumber');
                $table->date('dateHired');
                $table->string('role')->default('admin');
                $table->string('password');
                $table->integer('authorityLevel')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('schedules')) {
            Schema::create('schedules', function (Blueprint $table) {
                $table->bigIncrements('scheduleId');
                $table->unsignedBigInteger('staffId');
                $table->string('dayOfWeek');
                $table->time('startTime');
                $table->time('endTime');
                $table->boolean('isAvailable')->default(true);
                $table->timestamps();
                $table->index(['staffId', 'dayOfWeek']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('receptionists');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('staff');
    }
};


