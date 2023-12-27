<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHelpdeskTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('helpdesk_tickets', function (Blueprint $table) {
            $table->string('time_to_complete')->nullable();
            $table->datetime('complete_date')->nullable()->change();
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
            $table->dropColumn('time_to_complete');
        });
    }
}
