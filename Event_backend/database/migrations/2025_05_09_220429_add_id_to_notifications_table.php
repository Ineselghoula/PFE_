<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdToNotificationsTable extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Supprimer la clé primaire composite existante
            $table->dropPrimary(['user_id', 'evenement_id']);

            // Ajouter une nouvelle colonne 'id' auto-incrémentée
            $table->id(); // Cela ajoute une colonne 'id' comme clé primaire auto-incrémentée

            // Redéfinir les clés étrangères à nouveau après modification
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('evenement_id')->references('id')->on('evenements')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Supprimer les clés étrangères
            $table->dropForeign(['user_id']);
            $table->dropForeign(['evenement_id']);

            // Supprimer la colonne 'id'
            $table->dropColumn('id');

            // Recréer la clé primaire composite
            $table->primary(['user_id', 'evenement_id']);
        });
    }
}
