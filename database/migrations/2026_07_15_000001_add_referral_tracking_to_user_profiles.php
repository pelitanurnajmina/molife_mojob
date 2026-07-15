<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Siapa yang mengundang user ini (user_id pengundang).
            $table->unsignedBigInteger('referred_by')->nullable()->index()->after('referral_code');
            // Komisi referral hanya untuk PEMBAYARAN PERTAMA; flag ini mencegah dobel.
            $table->boolean('ref_credited')->default(false)->after('referred_by');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['referred_by', 'ref_credited']);
        });
    }
};
