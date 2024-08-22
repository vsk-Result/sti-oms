<?php

use Illuminate\Database\Migrations\Migration;
use App\Services\PermissionService;

class AddPivotCashFlowPermissions extends Migration
{
    private array $permissions = [
        'Отчет CASH FLOW' => ['pivot-cash-flow' => ['index']],
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

    public function down()
    {
        $this->permissionService->destroyPermissions($this->permissions);
    }
}
