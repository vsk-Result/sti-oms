<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashFlowPlanPaymentsTable extends Migration
{
    private array $permissions = [
        'Cash-flow план расходов' => ['cash-flow-plan-payments' => ['index']],
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function up()
    {
        $this->permissionService->createPermissions($this->permissions);

        Schema::create('cash_flow_plan_payments_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->string('name');
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
        });

        Schema::create('cash_flow_plan_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('object_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->string('name');
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
        });

        Schema::create('cash_flow_plan_payment_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('payment_id');
            $table->date('date');
            $table->decimal('amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->permissionService->destroyPermissions($this->permissions);
        Schema::dropIfExists('cash_flow_plan_payment_entries');
        Schema::dropIfExists('cash_flow_plan_payments');
        Schema::dropIfExists('cash_flow_plan_payments_groups');
    }
}
