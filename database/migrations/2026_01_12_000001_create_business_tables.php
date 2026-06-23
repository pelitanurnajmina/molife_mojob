<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Proposals / clients (one record = one client engagement / proposal)
        Schema::create('business_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('client_name');
            $table->string('industry')->nullable();      // bidang client
            $table->string('address')->nullable();       // alamat client
            $table->string('contact')->nullable();       // narahubung / telp / email
            $table->string('product')->nullable();       // produk yang ditawarkan
            $table->unsignedBigInteger('value')->nullable(); // nilai proposal (Rp)
            $table->string('status')->default('lead');   // lead|sent|negotiation|won|lost
            $table->date('proposal_date')->nullable();
            $table->text('notes')->nullable();           // respon client / catatan
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        // Business documents & files
        Schema::create('business_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('kind')->default('link');     // link|file
            $table->string('title');
            $table->string('url')->nullable();           // for links
            $table->string('path')->nullable();          // for files (storage)
            $table->string('original_name')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_deals');
        Schema::dropIfExists('business_docs');
    }
};
