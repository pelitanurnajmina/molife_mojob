<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penanda akun peraga (showcase) untuk kebutuhan konten/marketing.
     * HANYA akun dengan flag ini yang memuat animasi isi-otomatis.
     * User biasa tidak terpengaruh sama sekali.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_showcase')->default(false)->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_showcase');
        });
    }
};
