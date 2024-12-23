<?php

use App\Models\CRONProcess;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCronProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CRONProcess::truncate();
        Schema::table('cron_processes', function (Blueprint $table) {
            $table->dateTime('last_running_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_processes', function (Blueprint $table) {
            $table->dropColumn('last_running_date');
        });
    }
}
