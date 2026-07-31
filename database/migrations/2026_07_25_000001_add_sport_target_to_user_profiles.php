<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Target hari aktif olahraga per minggu untuk pilar Health di Life Score.
            $table->unsignedTinyInteger('sport_target')->default(4)->after('gender');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('sport_target');
        });
    }
};
