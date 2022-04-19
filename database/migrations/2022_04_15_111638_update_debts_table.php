<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->string('category', 30)->nullable();
            $table->string('code', 6)->nullable();
            $table->string('invoice_number', 50)->nullable();
            $table->string('order_author', 50)->nullable();
            $table->string('description', 250)->nullable();
            $table->string('comment', 250)->nullable();
            $table->date('invoice_payment_due_date')->nullable();
            $table->decimal('invoice_amount', 19, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn([
                'category', 'code', 'invoice_number', 'order_author',
                'description', 'comment', 'invoice_payment_due_date', 'invoice_amount'
            ]);
        });
    }
}
