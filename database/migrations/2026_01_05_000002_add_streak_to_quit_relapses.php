<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quit_relapses', function (Blueprint $table) {
            $table->unsignedInteger('streak')->default(0)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('quit_relapses', function (Blueprint $table) {
            $table->dropColumn('streak');
        });
    }
};
