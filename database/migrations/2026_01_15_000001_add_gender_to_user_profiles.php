<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $t) {
            // 'male' | 'female' | null
            $t->string('gender')->nullable()->after('religion');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $t) {
            $t->dropColumn('gender');
        });
    }
};
