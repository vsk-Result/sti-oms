<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('loans_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->date('date')->nullable();
            $table->date('planned_refund_date')->nullable();
            $table->date('refund_date')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
            $table->decimal('percent',  19, 4)->default(0);
            $table->string('description', 1000)->nullable();
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans_history');
    }
}
