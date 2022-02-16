<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToBankGuaranteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_guarantees', function (Blueprint $table) {
            $table->string('currency', 3)->default('RUB');
            $table->decimal('currency_rate', 7, 4)->default(1);
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
            $table->dropColumn(['currency', 'currency_rate']);
        });
    }
}
