<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->date('date');
            $table->unsignedSmallInteger('start_min'); // menit dari tengah malam (0–1439)
            $table->unsignedSmallInteger('end_min');   // menit dari tengah malam (1–1440)
            $table->string('title', 200);
            $table->string('color', 20)->default('blue');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_blocks');
    }
};
