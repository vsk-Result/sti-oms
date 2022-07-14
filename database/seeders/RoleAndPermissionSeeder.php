<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Services\PermissionService;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'Панель администратора' => ['admin-sidebar-menu' => ['show']],
            'Пользователи' => ['admin-users' => ['index', 'show', 'create', 'edit']],
            'Роли доступа' => ['admin-roles' => ['index', 'create', 'edit']],
            'Менеджер логов' => ['admin-logs' => ['index', 'show']],
            'Загрузка оплат' => ['payment-imports' => ['index', 'show', 'create', 'edit']],
            'Оплаты' => ['payments' => ['index', 'show', 'create', 'edit']],
            'Компании' => ['companies' => ['index', 'show', 'create', 'edit']],
            'Контрагенты' => ['organizations' => ['index', 'show', 'create', 'edit']],
            'Объекты' => ['objects' => ['index', 'show', 'create', 'edit']],
        ];

        (new PermissionService())->createPermissions($permissions);

        $superAdminRole = Role::create([
            'name' => 'super-admin',
            'description' => 'GOD',
        ]);

        User::find(1)->assignRole($superAdminRole);
    }
}
