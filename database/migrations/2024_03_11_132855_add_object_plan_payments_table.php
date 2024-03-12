<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObjectPlanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_plan_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('object_id');
            $table->unsignedTinyInteger('created_by_user_id')->nullable();
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->string('field');
            $table->decimal('amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('type_id')->default(0);
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
        Schema::dropIfExists('object_plan_payments');
    }
}
