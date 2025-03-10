<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->boolean('email_verified')->default(false);
            $table->string('verification_code', 4)->nullable();
            $table->timestamp('code_expires_at')->nullable();
            $table->string('image')->nullable();
            $table->string('password');
            $table->string('phone');
            $table->string('role')->default('participant');
            $table->boolean('actif')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};