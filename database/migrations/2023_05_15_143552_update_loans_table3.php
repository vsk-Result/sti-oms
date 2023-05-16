<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoansTable3 extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->boolean('is_auto_paid')->default(false);
            $table->decimal('paid_amount', 19, 4)->default(0);
            $table->string('search_name', 1000)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'is_auto_paid',
                'paid_amount'
            ]);
        });
    }
}
