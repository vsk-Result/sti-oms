<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreOrUpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(): View
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissionCategories = Permission::all()->groupBy('category');
        return view('roles.create', compact('permissionCategories'));
    }

    public function store(StoreOrUpdateRoleRequest $request): RedirectResponse
    {
        $this->roleService->createRole($request->toArray());
        return redirect()->route('roles.index');
    }

    public function edit(Role $role): View
    {
        $permissionCategories = Permission::all()->groupBy('category');
        return view('roles.edit', compact('role', 'permissionCategories'));
    }

    public function update(Role $role, StoreOrUpdateRoleRequest $request): RedirectResponse
    {
        $this->roleService->updateRole($role, $request->toArray());
        return redirect()->route('roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->roleService->destroyRole($role);
        return redirect()->route('roles.index');
    }
}
