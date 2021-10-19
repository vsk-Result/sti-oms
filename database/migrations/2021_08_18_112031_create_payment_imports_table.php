<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type_id');
            $table->unsignedTinyInteger('bank_id')->nullable();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->date('date');
            $table->integer('payments_count')->default(0);
            $table->decimal('amount_pay', 19, 4)->default(0);
            $table->decimal('amount_receive', 19, 4)->default(0);
            $table->decimal('incoming_balance', 19, 4)->nullable();
            $table->decimal('outgoing_balance', 19, 4)->nullable();
            $table->string('file')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('payment_imports');
    }
}
