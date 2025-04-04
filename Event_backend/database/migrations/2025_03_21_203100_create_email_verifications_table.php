<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email'); // Supprimé la contrainte unique
            $table->string('verification_code', 6); // Limité à 6 caractères
            $table->timestamp('expires_at')->index(); // Ajout d'un index
            $table->timestamps();

            // Contrainte unique combinée pour éviter les doublons
            $table->unique(['email', 'verification_code']);

            // Relation avec la table users (si applicable)
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_verifications');
    }
}