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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained()->onDelete('restrict');
            $table->string('phone', 20);
            $table->string('license_number', 50)->unique();
            $table->text('qualifications')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Indexes for faster queries
            $table->index('user_id');
            $table->index('specialty_id');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
