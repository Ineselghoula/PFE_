<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIsApprovedColumnInOrganisateurs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisateurs', function (Blueprint $table) {
            $table->renameColumn('`is-approved`', 'is_approved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisateurs', function (Blueprint $table) {
            $table->renameColumn('`is-approved`', 'is_approved');

        });
    }
}
