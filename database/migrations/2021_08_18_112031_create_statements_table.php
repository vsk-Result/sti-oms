<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bank_id');
            $table->unsignedTinyInteger('company_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->date('date');
            $table->integer('payments_count')->default(0);
            $table->decimal('amount_pay', 19, 4)->default(0);
            $table->decimal('amount_receive', 19, 4)->default(0);
            $table->decimal('incoming_balance', 19, 4)->nullable();
            $table->decimal('outgoing_balance', 19, 4)->nullable();
            $table->string('file');
            $table->unsignedTinyInteger('status_id')->default(0);
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
        Schema::dropIfExists('statements');
    }
}
