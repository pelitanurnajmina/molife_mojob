<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Days excused from wajib prayers (e.g. haid/nifas for women).
     * Such days do not break the prayer streak and are not counted as missed.
     */
    public function up(): void
    {
        Schema::create('sholat_excused_days', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->timestamps();
            $t->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sholat_excused_days');
    }
};
