<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Status;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function index(): View
    {
        $users = User::withTrashed()->with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        if (! (auth()->user()->can('edit admin-users') || auth()->id() === $user->id)) {
            abort(403);
        }
        $roles = Role::all();
        $permissionCategories = Permission::all()->groupBy('category');
        $statuses = Status::getStatuses();
        return view('users.edit', compact('user', 'statuses', 'roles', 'permissionCategories'));
    }

    public function update(User $user, UpdateUserRequest $request): RedirectResponse
    {
        if (! (auth()->user()->can('edit admin-users') || auth()->id() === $user->id)) {
            abort(403);
        }
        $this->userService->updateUser($user, $request->toArray());
        return redirect()->back();
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->destroyUser($user);
        return redirect()->back();
    }
}
