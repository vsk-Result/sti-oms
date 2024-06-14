<?php

use Illuminate\Database\Migrations\Migration;
use App\Services\PermissionService;

class CreateUploadDebtsStatusPermission extends Migration
{
    private array $permissions = [
        'Статус загруженных файлов по долгам объектов' => ['upload-debts-status' => ['index']],
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
