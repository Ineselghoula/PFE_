<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedToEvenementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->boolean('approved')->default(false); // Le champ 'approved' est par défaut à 'false'.
        });
    }
    
    public function down()
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
}    