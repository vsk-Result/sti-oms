<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotObjectDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_object_debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('object_id');
            $table->date('date');
            $table->json('provider');
            $table->json('contractor');
            $table->json('service');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_object_debts');
    }
}
