<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class CreateObjectUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('user_id');
            $table->unsignedSmallInteger('object_id');
            $table->timestamps();
        });

        Role::create([
            'name' => 'object-leader',
            'description' => 'Для руководителя объекта, смотреть оплаты',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', 'object-leader')->forceDelete();
        Schema::dropIfExists('object_user');
    }
}
