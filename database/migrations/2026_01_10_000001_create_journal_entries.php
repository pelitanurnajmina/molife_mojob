<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('template')->default('loa');
            $table->json('content')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'date', 'template']);
            $table->index(['user_id', 'template']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
