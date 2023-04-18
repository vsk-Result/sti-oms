<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuaranteePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guarantees', function (Blueprint $table) {
            $table->decimal('amount_payments', 19, 4)->default(0);
        });

        Schema::create('guarantee_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedSmallInteger('object_id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedSmallInteger('organization_id')->nullable();
            $table->unsignedBigInteger('guarantee_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->string('currency', 3)->default('RUB');
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
        Schema::dropIfExists('guarantee_payments');

        Schema::table('guarantees', function (Blueprint $table) {
            $table->dropColumn(['amount_payments']);
        });
    }
}
