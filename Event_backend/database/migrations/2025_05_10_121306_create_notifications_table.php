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
     */
  public function up()
{
    Schema::create('notifications', function (Blueprint $table) {
        $table->id();
        $table->string('name_evenement');
        $table->string('type');
        $table->timestamp('envoye_le')->nullable();
        $table->text('contenu');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('evenement_id')->nullable()->constrained()->onDelete('set null'); // Rendre nullable
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
        Schema::dropIfExists('notifications');
    }
}
