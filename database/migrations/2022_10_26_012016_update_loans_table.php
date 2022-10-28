<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLoansTable extends Migration
{
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('total_amount', 19, 4)->default(0);
        });

        Schema::create('loan_notify_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->string('tag');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_notify_tags');
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'total_amount'
            ]);
        });
    }
}
