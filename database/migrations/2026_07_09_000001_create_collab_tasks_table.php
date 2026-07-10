<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Papan tugas kanban per proyek kolaborasi bisnis.
        Schema::create('collab_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_product_id')->index();
            $table->string('title', 200);
            $table->string('note', 500)->nullable();
            $table->string('status', 12)->default('todo')->index(); // todo|progress|review|done
            $table->unsignedBigInteger('assignee_id')->nullable()->index(); // user owner/kolaborator
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collab_tasks');
    }
};
