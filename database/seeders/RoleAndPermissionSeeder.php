<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

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

        $roles = [
            'Панель администратора' => ['admin-sidebar-menu' => ['show']],
            'Пользователи' => ['admin-users' => ['index', 'show', 'create', 'edit']],
            'Роли доступа' => ['admin-roles' => ['index', 'create', 'edit']],
            'Менеджер логов' => ['admin-logs' => ['index', 'show']],
            'Загрузка оплат' => ['payment-imports' => ['index', 'show', 'create', 'edit']],
            'Оплаты' => ['payments' => ['index', 'show', 'create', 'edit']],
            'Компании' => ['companies' => ['index', 'show', 'create', 'edit']],
            'Организации' => ['organizations' => ['index', 'show', 'create', 'edit']],
            'Объекты' => ['objects' => ['index', 'show', 'create', 'edit']],
        ];

        foreach ($roles as $category => $role) {
            foreach ($role as $name => $rolePrefixes) {
                foreach ($rolePrefixes as $prefix) {
                    Permission::create([
                        'category' => $category,
                        'name' => "$prefix $name"
                    ]);
                }
            }
        }

        $superAdminRole = Role::create([
            'name' => 'super-admin',
            'description' => 'GOD',
        ]);

        User::find(1)->assignRole($superAdminRole);

        $testRole = Role::create([
            'name' => 'test-role',
            'description' => 'Роль для тестового пользователя',
        ]);

        User::find(2)->assignRole($testRole);
    }
}
