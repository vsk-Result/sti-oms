<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserInDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debt_imports', function (Blueprint $table) {
            $table->unsignedSmallInteger('created_by_user_id')->nullable()->change();
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedSmallInteger('created_by_user_id')->nullable()->change();
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
            //
        });
    }
}
