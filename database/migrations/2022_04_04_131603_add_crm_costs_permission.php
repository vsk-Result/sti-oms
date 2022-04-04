<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;

class AddCrmCostsPermission extends Migration
{
    private array $permissions = [
        'Касса CRM' => ['crm-costs' => ['index']],
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
