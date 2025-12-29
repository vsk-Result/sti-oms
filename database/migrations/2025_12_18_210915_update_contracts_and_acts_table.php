<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContractsAndActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedSmallInteger('organization_id')->nullable();
        });

        Schema::table('contract_avanses', function (Blueprint $table) {
            $table->unsignedTinyInteger('type_id')->default(0);
        });

        Schema::table('contract_avanses_received', function (Blueprint $table) {
            $table->unsignedTinyInteger('type_id')->default(0);
        });

        Schema::table('acts', function (Blueprint $table) {
            $table->decimal('amount_avans_float', 19, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_avanses', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });

        Schema::table('contract_avanses_received', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });

        Schema::table('acts', function (Blueprint $table) {
            $table->dropColumn('amount_avans_float');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
    }
}
