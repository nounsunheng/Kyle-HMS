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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('restrict');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->date('visit_date');
            $table->text('diagnosis');
            $table->text('treatment')->nullable();
            $table->text('prescription')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('appointment_id');
            $table->index('visit_date');
            $table->index(['patient_id', 'visit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
