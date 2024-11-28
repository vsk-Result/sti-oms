<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrmCashChecksTable extends Migration
{
    private array $permissions = [
        'Проверка касс ЦРМ' => ['crm-cash-check' => ['index']],
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function up()
    {
        Schema::create('crm_cash_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('crm_user_id');
            $table->unsignedTinyInteger('crm_cost_id');
            $table->string('period', 30);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
        });

        Schema::create('crm_cash_check_managers', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('check_id');
            $table->unsignedTinyInteger('manager_id');
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
        });

        $this->permissionService->createPermissions($this->permissions);
    }

    public function down()
    {
        $this->permissionService->destroyPermissions($this->permissions);

        Schema::dropIfExists('crm_cash_check_managers');
        Schema::dropIfExists('crm_cash_checks');
    }
}
