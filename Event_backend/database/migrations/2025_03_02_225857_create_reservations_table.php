<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('evenement_id');
        $table->unsignedBigInteger('participant_id')->nullable()->change();
        $table->string('full_name');
        $table->string('numero_telephone');
        $table->string('email');
        $table->integer('quantity');
        $table->string('code_res')->unique()->nullable();
       $table->timestamps();
    
        // Foreign keys
        $table->foreign('evenement_id')->references('id')->on('evenements')->onDelete('cascade');
        $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
    });
    
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
