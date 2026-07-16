<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Awal komunikasi dengan klien (email/whatsapp/sosmed/rekomendasi/dll).
        Schema::table('business_deals', function (Blueprint $table) {
            $table->string('channel', 30)->nullable()->after('contact');
        });

        Schema::table('collab_tasks', function (Blueprint $table) {
            // Tenggat tugas.
            $table->date('due_date')->nullable()->after('status');
        });

        // Tugas umum bisnis (tanpa proyek): product_id boleh kosong.
        DB::statement('ALTER TABLE collab_tasks MODIFY business_product_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('business_deals', function (Blueprint $table) {
            $table->dropColumn('channel');
        });
        Schema::table('collab_tasks', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
        DB::statement('ALTER TABLE collab_tasks MODIFY business_product_id BIGINT UNSIGNED NOT NULL');
    }
};
