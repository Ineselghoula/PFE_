<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvenementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->string('image');
            $table->string('map_link');
            $table->decimal('prix', 8, 2);
            $table->string('adresse');
            $table->time('temps');
            $table->string('etat');
            $table->integer('nbr_place');

            // Clés étrangères
            $table->unsignedBigInteger('organisateur_id');
            $table->foreign('organisateur_id')->references('id')->on('organisateurs')->onDelete('cascade');

            $table->unsignedBigInteger('sous_categorie_id');
            $table->foreign('sous_categorie_id')->references('id')->on('sous_categories')->onDelete('cascade');

            // Champ d'approbation
            $table->boolean('approved')->default(false);

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
        Schema::dropIfExists('evenements');
    }
}
