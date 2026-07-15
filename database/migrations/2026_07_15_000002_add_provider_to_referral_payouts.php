<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_payouts', function (Blueprint $table) {
            // Nama bank / jenis e-wallet tujuan pencairan (BCA, DANA, GoPay, dll).
            $table->string('provider', 30)->nullable()->after('method');
        });
    }

    public function down(): void
    {
        Schema::table('referral_payouts', function (Blueprint $table) {
            $table->dropColumn('provider');
        });
    }
};
