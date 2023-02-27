<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->string('contract', 300)->nullable();
            $table->decimal('guarantee', 19, 4)->default(0);
            $table->decimal('avans', 19, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn(['contract', 'avans', 'guarantee']);
        });
    }
}
