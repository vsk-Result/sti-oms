<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency', 3)->default('RUB');
            $table->decimal('currency_rate', 7, 4)->default(1);
            $table->decimal('currency_amount', 19, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['currency', 'currency_rate', 'currency_amount']);
        });
    }
}
