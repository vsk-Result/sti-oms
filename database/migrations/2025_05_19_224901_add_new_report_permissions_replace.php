<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;

class AddNewReportPermissionsReplace extends Migration
{
    private array $oldPermissions = [
        'Расчет стоимости рабочих' => ['index pivot-calculate-workers-cost' => ['index']],
    ];

    private array $newPermissions = [
        'Расчет стоимости рабочих' => ['pivot-calculate-workers-cost' => ['index']],
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function up()
    {
        $this->permissionService->destroyPermissions($this->oldPermissions);
        $this->permissionService->createPermissions($this->newPermissions);
    }

    public function down()
    {
        $this->permissionService->destroyPermissions($this->newPermissions);
    }
}
