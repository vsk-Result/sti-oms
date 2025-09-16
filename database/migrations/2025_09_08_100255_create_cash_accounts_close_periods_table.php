<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashAccountsClosePeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_accounts_close_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('cash_account_id');
            $table->date('period');
            $table->string('payments_count');
            $table->decimal('payments_amount', 19, 4)->default(0);
            $table->decimal('balance_when_closed', 19, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_accounts_close_periods');
    }
}
