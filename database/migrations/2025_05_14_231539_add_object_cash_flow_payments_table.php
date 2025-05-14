<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObjectCashFlowPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('object_cash_flow_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('object_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedSmallInteger('organization_id');
            $table->date('date');
            $table->decimal('amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('category_id')->default(0);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('object_cash_flow_payments');
    }
}
