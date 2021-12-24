<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PermissionService;

class CreateActsTable extends Migration
{
    private array $permissions = [
        'Акты' => ['acts' => ['index', 'show', 'create', 'edit']],
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
        Schema::create('acts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedTinyInteger('company_id');
            $table->unsignedSmallInteger('object_id');
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 19, 4)->default(0);
            $table->decimal('amount_avans', 19, 4)->default(0);
            $table->decimal('amount_deposit', 19, 4)->default(0);
            $table->decimal('amount_need_paid', 19, 4)->default(0);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('acts');
    }
}
