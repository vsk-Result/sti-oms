<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\PermissionService;

class CreateCronProcessesTable extends Migration
{
    private array $permissions = [
        'Статус фоновых процессов' => ['cron-processes' => ['index']]
    ];

    private PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function up()
    {
        Schema::create('cron_processes', function (Blueprint $table) {
            $table->id();
            $table->string('command');
            $table->string('title');
            $table->string('description', 500)->nullable();
            $table->string('period');
            $table->dateTime('last_executed_date')->nullable();
            $table->text('last_error')->nullable();
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
        Schema::dropIfExists('cron_processes');
    }
}
