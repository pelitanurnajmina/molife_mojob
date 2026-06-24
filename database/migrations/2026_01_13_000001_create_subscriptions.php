<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan');                 // 1 | 3 | 6 | 12 (months key)
            $table->unsignedSmallInteger('months');
            $table->unsignedInteger('price');
            $table->string('status')->default('active'); // active | expired
            $table->string('ref')->nullable();      // payment reference
            $table->date('starts_at');
            $table->date('ends_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
