<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyToActsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acts', function (Blueprint $table) {
            $table->string('currency', 3)->default('RUB');
            $table->decimal('currency_rate', 7, 4)->default(1);
        });

        Schema::table('act_payments', function (Blueprint $table) {
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
        Schema::table('act_payments', function (Blueprint $table) {
            $table->dropColumn(['currency', 'currency_rate']);
        });

        Schema::table('acts', function (Blueprint $table) {
            $table->dropColumn(['currency', 'currency_rate']);
        });
    }
}
