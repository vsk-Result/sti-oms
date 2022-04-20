<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PermissionService;

class CreateLoansTable extends Migration
{
    private array $permissions = [
        'Займы/Кредиты' => ['loans' => ['index', 'show', 'create', 'edit']],
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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type_id');
            $table->unsignedTinyInteger('bank_id')->nullable();
            $table->unsignedSmallInteger('organization_id')->nullable();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->string('name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('percent', 7, 4)->default(0);
            $table->decimal('amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->string('description', 1000)->nullable();
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
        Schema::dropIfExists('loans');
    }
}
