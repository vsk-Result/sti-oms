<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PermissionService;

class CreateObjectTransferServiceTable extends Migration
{
    private array $permissions = [
        'Распределение услуг на трансфер' => ['distribution-transfer-service' => ['index']],
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function up()
    {
        Schema::create('object_transfer_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('object_id');
            $table->decimal('amount', 19, 4)->default(0);
            $table->timestamps();

            $this->permissionService->createPermissions($this->permissions);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->permissionService->destroyPermissions($this->permissions);
        Schema::dropIfExists('object_transfer_service');
    }
}
