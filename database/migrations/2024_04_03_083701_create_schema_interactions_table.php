<?php

use App\Services\PermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchemaInteractionsTable extends Migration
{
    private array $permissions = [
        'Схема взаимодействия' => ['schema-interactions' => ['index', 'edit']]
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
        Schema::create('schema_interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('created_by_user_id');
            $table->unsignedTinyInteger('updated_by_user_id')->nullable();
            $table->string('name');
            $table->decimal('amount', 19, 4)->default(0);
            $table->string('currency', 3)->default('RUB');
            $table->unsignedTinyInteger('status_id')->default(0);
            $table->timestamps();
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
        Schema::dropIfExists('schema_interactions');
    }
}
