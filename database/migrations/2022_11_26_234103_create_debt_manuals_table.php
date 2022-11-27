<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebtManualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debt_manuals', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type_id');
            $table->unsignedSmallInteger('object_id');
            $table->unsignedSmallInteger('object_worktype_id')->nullable();
            $table->unsignedSmallInteger('organization_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
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
        Schema::dropIfExists('debt_manuals');
    }
}
