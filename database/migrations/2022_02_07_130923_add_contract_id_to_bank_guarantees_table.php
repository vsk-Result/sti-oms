<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractIdToBankGuaranteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_guarantees', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedSmallInteger('organization_id')->nullable();
            $table->string('number', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_guarantees', function (Blueprint $table) {
            $table->dropColumn(['contract_id', 'organization_id', 'number']);
        });
    }
}
