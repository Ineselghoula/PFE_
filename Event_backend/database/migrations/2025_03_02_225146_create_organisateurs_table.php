<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisateursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('organisateurs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->string('nom_societe');
        $table->string('site_web')->nullable();
        $table->string('reseau_social')->nullable();
        $table->text('biographie')->nullable();
        $table->boolean('is_approved')->default(false); // pour l'activation des comptes organisateurs par l'admin
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organisateurs');
    }
}
