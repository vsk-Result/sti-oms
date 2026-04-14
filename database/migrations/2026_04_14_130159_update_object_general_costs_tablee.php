<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateObjectGeneralCostsTablee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('object_general_costs', function (Blueprint $table) {
            $table->decimal('amount_without_nds', 19, 4)->default(0);
            $table->string('nds_type')->default('nds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('object_general_costs', function (Blueprint $table) {
            $table->dropColumn(['amount_without_nds', 'nds_type']);
        });
    }
}
