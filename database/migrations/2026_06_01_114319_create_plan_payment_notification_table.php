<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PermissionService;

class CreatePlanPaymentNotificationTable extends Migration
{
    private array $permissions = [
        'Уведомления о планах Cash Flow' => ['cash-flow-notifications' => ['index']]
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
        Schema::table('cash_flow_plan_payments', function (Blueprint $table) {
            $table->boolean('need_notification')->default(false);
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
        Schema::table('cash_flow_plan_payments', function (Blueprint $table) {
            $table->dropColumn(['need_notification']);
        });
    }
}
