<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;

class CreateCashAccountsValidatePermissions extends Migration
{
    private array $permissions = [
        'Возможность закрывать оплаты доступных касс' => ['cash-accounts-validate' => ['index']],
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
