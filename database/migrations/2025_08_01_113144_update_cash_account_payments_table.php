<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCashAccountPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('crm_user_id')->nullable();
        });

        Schema::table('cash_account_payments', function (Blueprint $table) {
            $table->unsignedTinyInteger('crm_avans_id')->nullable();
            $table->unsignedSmallInteger('object_worktype_id')->nullable();
            $table->unsignedBigInteger('crm_employee_id')->nullable();
            $table->string('crm_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_account_payments', function (Blueprint $table) {
            $table->dropColumn(['crm_avans_id', 'crm_employee_id', 'crm_date', 'object_worktype_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('crm_user_id');
        });
    }
}
