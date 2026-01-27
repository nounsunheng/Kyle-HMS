<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('allergies');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });
    }
};
