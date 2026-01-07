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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_per_appointment')->default(30); // minutes
            $table->integer('max_appointments')->default(0);
            $table->integer('booked_appointments')->default(0);
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->timestamps();

            // Indexes for faster queries
            $table->index('doctor_id');
            $table->index('schedule_date');
            $table->index(['doctor_id', 'schedule_date']);
            $table->index('status');

            // Unique constraint: doctor can only have one schedule per day
            $table->unique(['doctor_id', 'schedule_date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
