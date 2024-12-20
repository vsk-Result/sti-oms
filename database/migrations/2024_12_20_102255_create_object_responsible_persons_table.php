<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectResponsiblePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_responsible_persons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('object_id')->nullable();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedTinyInteger('position_id');
            $table->string('fullname');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedTinyInteger('status_id')->default(0);
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
        Schema::dropIfExists('object_responsible_persons');
    }
}
