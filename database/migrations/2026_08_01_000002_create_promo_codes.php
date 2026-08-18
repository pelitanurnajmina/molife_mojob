<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kode promo influencer/afiliasi.
     * - discount_percent  : diskon untuk AUDIENS yang daftar pakai kode (pembayaran pertama).
     * - commission_percent: komisi untuk INFLUENCER dari pembayaran audiensnya.
     * Keduanya berlaku untuk pembayaran pertama saja (dijaga flag ref_credited),
     * sama seperti program referral biasa — hanya rate-nya bisa diatur per kode.
     */
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $t->string('label')->nullable();                       // catatan, mis. "Budi - Instagram"
            $t->unsignedTinyInteger('discount_percent')->default(0);   // 0-100
            $t->unsignedTinyInteger('commission_percent')->default(0); // 0-100
            $t->boolean('active')->default(true);
            $t->unsignedInteger('max_uses')->nullable();           // null = tak terbatas
            $t->unsignedInteger('used_count')->default(0);         // jumlah audiens yang daftar pakai kode
            $t->unsignedInteger('paid_count')->default(0);         // jumlah audiens yang sudah bayar
            $t->date('expires_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
