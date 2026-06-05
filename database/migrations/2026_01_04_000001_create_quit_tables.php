<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One tracker per user per habit type (porn, sosmed)
        Schema::create('quit_trackers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('type');                       // porn | sosmed
            $t->date('start_date');                   // streak start (resets on relapse)
            $t->unsignedInteger('best_streak')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'type']);
        });

        // Relapse history
        Schema::create('quit_relapses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('type');
            $t->date('date');
            $t->string('note')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quit_relapses');
        Schema::dropIfExists('quit_trackers');
    }
};
