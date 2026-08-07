<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idea_scripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('type', 10)->default('idea'); // idea | script
            $table->string('title', 200);
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idea_scripts');
    }
};
