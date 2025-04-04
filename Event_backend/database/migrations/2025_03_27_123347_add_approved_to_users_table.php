<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
    $table->tinyInteger('is_approved')->default(0);  // Ajoutez cette ligne si elle manque
});

    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('approved', 'is_approved');
        });
    }
    
    
}
