<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateObjectIdNullableToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->unsignedSmallInteger('object_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            //
        });
    }
}
