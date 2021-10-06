<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use Spatie\Permission\Models\Role;

class RoleService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createRole(array $requestData): void
    {
        $role = Role::create([
            'name' => $this->sanitizer->set($requestData['name'])->lowerCase()->replace(' ', '-')->get(),
            'description' => $this->sanitizer->set($requestData['description'])->upperCaseFirstWord()->get()
        ]);

        $role->syncPermissions($requestData['permissions'] ?? []);
    }

    public function updateRole(Role $role, array $requestData): void
    {
        $role->update([
            'name' => $this->sanitizer->set($requestData['name'])->lowerCase()->replace(' ', '-')->get(),
            'description' => $this->sanitizer->set($requestData['description'])->upperCaseFirstWord()->get()
        ]);

        $role->syncPermissions($requestData['permissions'] ?? []);
    }

    public function destroyRole(Role $role): void
    {
        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();
    }
}
