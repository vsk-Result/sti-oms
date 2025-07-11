<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashAccountsTable extends Migration
{
    private array $permissions = [
        'Учет наличных расходов объектов' => ['cash-accounts' => ['index', 'show', 'create', 'edit']],
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function up()
    {
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedTinyInteger('responsible_user_id');
            $table->string('name');
            $table->decimal('start_balance_amount', 19, 4)->default(0);
            $table->decimal('balance_amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cash_account_object', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('cash_account_id');
            $table->unsignedTinyInteger('object_id');
            $table->timestamps();
        });

        Schema::create('cash_account_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('cash_account_id');
            $table->unsignedTinyInteger('user_id');
            $table->unsignedTinyInteger('permission_id');
            $table->timestamps();
        });

        Schema::create('cash_account_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_account_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->unsignedSmallInteger('organization_id')->nullable();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedSmallInteger('object_id')->nullable();
            $table->unsignedTinyInteger('type_id')->default(0);
            $table->string('category', 20)->nullable();
            $table->string('code', 12)->nullable();
            $table->string('description', 1500)->nullable();
            $table->date('date');
            $table->decimal('amount', 19, 4)->default(0);
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $this->permissionService->createPermissions($this->permissions);
    }

    public function down()
    {
        $this->permissionService->destroyPermissions($this->permissions);
        Schema::dropIfExists('cash_account_payments');
        Schema::dropIfExists('cash_account_user');
        Schema::dropIfExists('cash_account_object');
        Schema::dropIfExists('cash_accounts');
    }
}
