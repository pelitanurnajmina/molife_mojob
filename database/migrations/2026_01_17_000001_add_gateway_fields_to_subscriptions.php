<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan data gateway Midtrans agar QR pending bisa dipakai ulang
     * (tidak membuat charge baru setiap halaman pembayaran dibuka).
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $t) {
            $t->text('qr_url')->nullable()->after('ref');
            $t->timestamp('qr_expires_at')->nullable()->after('qr_url');
            $t->string('midtrans_transaction_id')->nullable()->after('qr_expires_at');
            $t->json('gateway_payload')->nullable()->after('midtrans_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $t) {
            $t->dropColumn(['qr_url', 'qr_expires_at', 'midtrans_transaction_id', 'gateway_payload']);
        });
    }
};
