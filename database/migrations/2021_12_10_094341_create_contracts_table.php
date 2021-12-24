<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PermissionService;

class CreateContractsTable extends Migration
{
    private array $permissions = [
        'Договора' => ['contracts' => ['index', 'show', 'create', 'edit']],
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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedTinyInteger('type_id');
            $table->unsignedTinyInteger('company_id');
            $table->unsignedSmallInteger('object_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->string('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('amount_type_id')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('stage_id')->default(0);
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
        Schema::dropIfExists('contracts');
    }
}
