<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'expired' to the status enum for appointments table
        DB::statement("ALTER TABLE `appointments` MODIFY `status` ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show', 'expired') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'expired' from the status enum
        DB::statement("ALTER TABLE `appointments` MODIFY `status` ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') NOT NULL DEFAULT 'pending'");
    }
};
