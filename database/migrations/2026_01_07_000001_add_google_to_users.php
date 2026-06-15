<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('email')->nullable()->unique()->after('username');
            $table->string('name')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('name');
            $table->string('password')->nullable()->change(); // OAuth users have no password
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'email', 'name', 'avatar']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
