<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoansTable extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('search_name', 300)->nullable();
        });
    }

    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'search_name'
            ]);
        });
    }
}
