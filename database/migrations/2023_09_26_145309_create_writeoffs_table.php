<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PermissionService;

class CreateWriteoffsTable extends Migration
{
    private array $permissions = [
        'Списания' => ['writeoffs' => ['index', 'show', 'create', 'edit']]
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function up()
    {
        Schema::create('writeoffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedSmallInteger('object_id')->nullable();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
            $table->string('crm_employee_uid', 10)->nullable();
            $table->string('description', 1500)->nullable();
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
        Schema::dropIfExists('writeoffs');
    }
}
