<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('import_id')->nullable();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedTinyInteger('bank_id')->nullable();
            $table->unsignedSmallInteger('object_id')->nullable();
            $table->unsignedSmallInteger('object_worktype_id')->nullable();
            $table->unsignedSmallInteger('organization_sender_id')->nullable();
            $table->unsignedSmallInteger('organization_receiver_id')->nullable();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedTinyInteger('type_id')->default(0);
            $table->unsignedTinyInteger('payment_type_id');
            $table->string('category', 20)->nullable();
            $table->string('code', 6)->nullable();
            $table->string('description', 1500)->nullable();
            $table->date('date');
            $table->decimal('amount', 19, 4)->default(0);
            $table->decimal('amount_without_nds', 19, 4)->default(0);
            $table->unsignedTinyInteger('is_need_split')->default(0);
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
        Schema::dropIfExists('payments');
    }
}
