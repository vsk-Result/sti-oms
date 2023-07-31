<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDebtManualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debt_manuals', function (Blueprint $table) {
            $table->decimal('avans', 19, 4)->default(0);
            $table->decimal('guarantee', 19, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debt_manuals', function (Blueprint $table) {
            $table->dropColumn(['avans', 'guarantee']);
        });
    }
}
