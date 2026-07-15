<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalisasi nilai lama dulu supaya tidak ada yang gagal masuk enum.
        DB::table('referral_payouts')->where('status', 'done')->update(['status' => 'paid']);
        DB::table('referral_payouts')->whereNotIn('status', ['pending', 'paid', 'rejected'])->update(['status' => 'pending']);

        // ENUM: di phpMyAdmin kolom ini jadi dropdown pilihan, aman dari salah ketik.
        DB::statement("ALTER TABLE referral_payouts MODIFY status ENUM('pending','paid','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE referral_payouts MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");
    }
};
