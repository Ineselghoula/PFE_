<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEvenementsTableChangeCategorieToSousCategorie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evenements', function (Blueprint $table) {
            // Supprimer la clé étrangère et la colonne categorie_id si elle existe
            if (Schema::hasColumn('evenements', 'categorie_id')) {
                $table->dropForeign(['categorie_id']);
                $table->dropColumn('categorie_id');
            }

            // Ajouter la colonne sous_categorie_id si elle n'existe pas
            if (!Schema::hasColumn('evenements', 'sous_categorie_id')) {
                $table->unsignedBigInteger('sous_categorie_id')->after('organisateur_id');
                $table->foreign('sous_categorie_id')->references('id')->on('sous_categories')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evenements', function (Blueprint $table) {
            // Supprimer sous_categorie_id
            if (Schema::hasColumn('evenements', 'sous_categorie_id')) {
                $table->dropForeign(['sous_categorie_id']);
                $table->dropColumn('sous_categorie_id');
            }

            // Réajouter categorie_id si besoin
            if (!Schema::hasColumn('evenements', 'categorie_id')) {
                $table->unsignedBigInteger('categorie_id')->nullable();
                $table->foreign('categorie_id')->references('id')->on('categories')->onDelete('cascade');
            }
        });
    }
}
