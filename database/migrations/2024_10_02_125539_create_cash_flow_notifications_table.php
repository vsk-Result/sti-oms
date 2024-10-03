<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashFlowNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_flow_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedTinyInteger('target_user_id');
            $table->string('name');
            $table->string('description', 1000)->nullable();
            $table->unsignedTinyInteger('event_type_id')->default(0);
            $table->unsignedTinyInteger('type_id')->default(0);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->datetime('read_date')->nullable();
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
        Schema::dropIfExists('cash_flow_notifications');
    }
}
