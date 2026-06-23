<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "Alasan Besarku" (Big Why)
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->text('big_why')->nullable()->after('tour_done');
        });

        // Saved/favorite quotes
        Schema::create('quote_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->string('src')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });

        // Vision board items
        Schema::create('vision_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('emoji', 16)->nullable();
            $table->string('text');
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('big_why');
        });
        Schema::dropIfExists('quote_favorites');
        Schema::dropIfExists('vision_items');
    }
};
