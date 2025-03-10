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
            
            // Define foreign key columns first
            $table->unsignedBigInteger('organisateur_id');
            $table->unsignedBigInteger('categorie_id');
            
            // Add foreign key constraints
            $table->foreign('organisateur_id')->references('id')->on('organisateurs')->onDelete('cascade');
            $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('cascade');
            
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
