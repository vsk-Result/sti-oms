<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_exports', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->text('data', 1000)->nullable();
            $table->string('data_hash', 1000)->nullable();
            $table->string('model', 100);
            $table->string('filepath', 100);
            $table->string('filename', 100);
            $table->string('name', 100);
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
        Schema::dropIfExists('schedule_exports');
    }
}
