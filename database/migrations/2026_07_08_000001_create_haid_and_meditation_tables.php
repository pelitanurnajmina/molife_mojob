<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Siklus haid: satu baris = satu periode haid (mulai s.d. selesai).
        Schema::create('haid_cycles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null = masih berlangsung
            $table->string('note', 200)->nullable();
            $table->timestamps();
        });

        // Sesi meditasi yang selesai (pola sama dengan pomodoro_sessions).
        Schema::create('meditation_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->date('date')->index();
            $table->unsignedSmallInteger('minutes');
            $table->string('sound', 20)->nullable(); // hening|hujan|ombak|angin
            $table->string('note', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('haid_cycles');
        Schema::dropIfExists('meditation_sessions');
    }
};
