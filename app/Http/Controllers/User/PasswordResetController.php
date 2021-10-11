<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function edit(User $user): View
    {
        if (! (auth()->user()->can('edit admin-users') || auth()->id() === $user->id)) {
            abort(403);
        }
        return view('users.passwords-reset.edit', compact('user'));
    }

    public function update(User $user, UpdatePasswordRequest $request): RedirectResponse
    {
        if (! (auth()->user()->can('edit admin-users') || auth()->id() === $user->id)) {
            abort(403);
        }
        $this->userService->updatePassword($user, $request->toArray());
        return redirect()->route('users.edit', compact('user'));
    }
}
