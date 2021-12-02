<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class CreateBankGuaranteesTable extends Migration
{
    private array $roles = [
        'Банковские гарантии' => ['bank-guarantees' => ['index', 'show', 'create', 'edit']]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_guarantees', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedTinyInteger('company_id');
            $table->unsignedSmallInteger('object_id');
            $table->unsignedTinyInteger('bank_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
            $table->date('start_date_deposit')->nullable();
            $table->date('end_date_deposit')->nullable();
            $table->decimal('amount_deposit', 19, 4)->default(0);
            $table->string('target', 50)->nullable();
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        foreach ($this->roles as $category => $role) {
            foreach ($role as $name => $rolePrefixes) {
                foreach ($rolePrefixes as $prefix) {
                    Permission::create([
                        'category' => $category,
                        'name' => "$prefix $name"
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->roles as $category => $role) {
            foreach ($role as $name => $rolePrefixes) {
                foreach ($rolePrefixes as $prefix) {
                    $permission = Permission::where('category', $category)->where('name', "$prefix $name")->first();
                    $permission->forceDelete();
                }
            }
        }

        Schema::dropIfExists('bank_guarantees');
    }
}
