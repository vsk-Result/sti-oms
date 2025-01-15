<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePivotObjectDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pivot_object_debts', function (Blueprint $table) {
            $table->dropColumn(['provider', 'contractor', 'service']);
            $table->unsignedSmallInteger('debt_type_id');
            $table->unsignedSmallInteger('debt_source_id');
            $table->string('filepath');
            $table->decimal('avans', 19, 4)->default(0);
            $table->decimal('unwork_avans', 19, 4)->default(0);
            $table->decimal('guarantee', 19, 4)->default(0);
            $table->decimal('guarantee_deadline', 19, 4)->default(0);
            $table->decimal('amount', 19, 4)->default(0);
            $table->decimal('amount_fix', 19, 4)->default(0);
            $table->decimal('amount_float', 19, 4)->default(0);
            $table->decimal('amount_without_nds', 19, 4)->default(0);
            $table->decimal('balance_contract', 19, 4)->default(0);
            $table->json('details')->nullable();

            $table->datetime('date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pivot_object_debts', function (Blueprint $table) {
            $table->json('provider');
            $table->json('contractor');
            $table->json('service');
            $table->dropColumn([
                'debt_type_id', 'debt_source_id', 'filepath', 'avans', 'guarantee', 'amount', 'balance_contract',
                'amount_without_nds', 'details', 'unwork_avans', 'guarantee_deadline', 'amount_fix', 'amount_float'
            ]);
        });
    }
}
