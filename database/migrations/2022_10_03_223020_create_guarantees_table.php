<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PermissionService;

class CreateGuaranteesTable extends Migration
{
    private array $permissions = [
        'Гарантийные удержания' => ['guarantees' => ['index', 'show', 'create', 'edit']],
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guarantees', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('object_id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedSmallInteger('organization_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
            $table->decimal('fact_amount', 19, 4)->default(0);
            $table->string('currency', 3)->default('RUB');
            $table->decimal('currency_rate', 7, 4)->default(1);
            $table->boolean('has_bank_guarantee')->default(false);
            $table->boolean('has_final_act')->nullable();
            $table->string('state')->nullable();
            $table->string('conditions', 1000)->nullable();
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $this->permissionService->createPermissions($this->permissions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->permissionService->destroyPermissions($this->permissions);
        Schema::dropIfExists('guarantees');
    }
}
