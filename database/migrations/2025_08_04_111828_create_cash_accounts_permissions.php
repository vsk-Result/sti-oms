<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;

class CreateCashAccountsPermissions extends Migration
{
    private array $permissions = [
        'Возможность видеть все кассы' => ['cash-accounts-all-view' => ['index']],
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function up()
    {
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
    }
}
