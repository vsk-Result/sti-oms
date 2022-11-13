<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;

class AddCrmSplitAvansImportPermission extends Migration
{
    private array $permissions = [
        'CRM импорт авансов зарплат' => ['crm-split-avans-imports' => ['index']],
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
