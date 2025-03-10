<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('reservations', function (Blueprint $table) {
        $table->foreignId('participant_id')->constrained('participants');
        $table->foreignId('evenement_id')->constrained('evenements');
        $table->date('date_reservation');
        $table->integer('nbr_participant');
        $table->primary(['participant_id', 'evenement_id']);
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
        Schema::dropIfExists('reservations');
    }
}
