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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
            $table->string('appointment_number', 20)->unique();
            $table->time('appointment_time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index('patient_id');
            $table->index('schedule_id');
            $table->index('appointment_number');
            $table->index('status');
            $table->index(['patient_id', 'status']);
            $table->index(['schedule_id', 'appointment_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
