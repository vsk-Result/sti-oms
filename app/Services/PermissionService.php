<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function createPermissions(array $permissions): void
    {
        foreach ($permissions as $category => $permission) {
            foreach ($permission as $name => $permissionPrefixes) {
                foreach ($permissionPrefixes as $prefix) {
                    Permission::create([
                        'category' => $category,
                        'name' => "$prefix $name"
                    ]);
                }
            }
        }
    }

    public function destroyPermissions(array $permissions): void
    {
        foreach ($permissions as $category => $permission) {
            foreach ($permission as $name => $permissionPrefixes) {
                foreach ($permissionPrefixes as $prefix) {
                    $permission = Permission::where('category', $category)->where('name', "$prefix $name")->first();
                    if ($permission) {
                        $permission->forceDelete();
                    }
                }
            }
        }
    }
}
