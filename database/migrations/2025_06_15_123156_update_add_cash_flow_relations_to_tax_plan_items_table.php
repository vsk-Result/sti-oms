<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAddCashFlowRelationsToTaxPlanItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_flow_plan_payments', function (Blueprint $table) {
            $table->boolean('from_tax_plan')->default(false);
        });

        Schema::table('tax_plan_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('cash_flow_group_id')->nullable();
            $table->string('cash_flow_name')->nullable();
            $table->boolean('to_cash_flow')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_flow_plan_payments', function (Blueprint $table) {
            $table->dropColumn(['from_tax_plan']);
        });

        Schema::table('tax_plan_items', function (Blueprint $table) {
            $table->dropColumn(['cash_flow_group_id', 'cash_flow_name', 'to_cash_flow']);
        });
    }
}
