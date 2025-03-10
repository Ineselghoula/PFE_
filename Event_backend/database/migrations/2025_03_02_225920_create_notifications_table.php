<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */public function up()
{
    Schema::create('notifications', function (Blueprint $table) {
        $table->bigInteger('user_id')->unsigned();
        $table->bigInteger('evenement_id')->unsigned();
        $table->text('contenu');
        $table->string('type');
        $table->dateTime('date_envoi');
        $table->primary(['user_id', 'evenement_id']);
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('evenement_id')->references('id')->on('evenements')->onDelete('cascade');
        
        $table->timestamps();
    });
}
}