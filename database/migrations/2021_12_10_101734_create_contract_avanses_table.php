<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractAvansesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_avanses', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedSmallInteger('object_id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contract_avanses_received', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedSmallInteger('object_id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('contract_avanses_received');
        Schema::dropIfExists('contract_avanses');
    }
}
