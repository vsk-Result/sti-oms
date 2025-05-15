<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;

class AddNewReportPermissions extends Migration
{
    private array $permissions = [
        'Расчет стоимости рабочих' => ['index pivot-calculate-workers-cost' => ['index']],
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
