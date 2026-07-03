<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_collaborators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_product_id')->index();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('email')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // terisi setelah undangan diterima
            $table->string('token', 64)->unique();
            $table->string('status', 20)->default('pending'); // pending | active
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['business_product_id', 'email']);
        });

        // Template pesan dkk. bisa di-scope ke satu produk (untuk kolaborasi).
        Schema::table('business_docs', function (Blueprint $table) {
            $table->unsignedBigInteger('business_product_id')->nullable()->index()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_collaborators');
        Schema::table('business_docs', function (Blueprint $table) {
            $table->dropColumn('business_product_id');
        });
    }
};
