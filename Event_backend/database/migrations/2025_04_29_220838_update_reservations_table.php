<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'full_name')) {
                $table->string('full_name')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'numero_telephone')) {
                $table->string('numero_telephone')->nullable();
            }

            if (!Schema::hasColumn('reservations', 'email')) {
                $table->string('email')->nullable();
            }

            
           
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'full_name')) {
                $table->dropColumn('full_name');
            }

            if (Schema::hasColumn('reservations', 'numero_telephone')) {
                $table->dropColumn('numero_telephone');
            }

            if (Schema::hasColumn('reservations', 'email')) {
                $table->dropColumn('email');
            }
          


            
        });
    }
};
