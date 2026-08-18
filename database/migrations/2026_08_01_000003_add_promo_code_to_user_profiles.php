<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Kode promo influencer yang dipakai user ini saat daftar (menentukan
            // rate diskon & komisi). Null = daftar organik / referral biasa.
            $table->unsignedBigInteger('promo_code_id')->nullable()->index()->after('ref_credited');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('promo_code_id');
        });
    }
};
