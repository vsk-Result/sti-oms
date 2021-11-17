<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    private UploadService $uploadService;
    private Sanitizer $sanitizer;

    public function __construct(UploadService $uploadService, Sanitizer $sanitizer)
    {
        $this->uploadService = $uploadService;
        $this->sanitizer = $sanitizer;
    }

    public function updateUser(User $user, array $requestData): User
    {
        if (array_key_exists('photo', $requestData)) {
            $photo = $this->uploadService->uploadFile('users/photo', $requestData['photo']);
        } elseif ($requestData['avatar_remove'] === '1') {
            $photo = null;
        } else {
            $photo = $user->photo;
        }

        $user->update([
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseAllFirstWords()->get(),
            'email' => $this->sanitizer->set($requestData['email'])->lowerCase()->get(),
            'phone' => $this->sanitizer->set($requestData['phone'])->toPhone()->get(),
            'photo' => $photo,
            'status_id' => $requestData['status_id'] ?? $user->status_id,
            'email_verified_at' => array_key_exists('email_verified_at', $requestData) ? $requestData['email_verified_at'] : $user->email_verified_at,
        ]);

        if (isset($requestData['status_id']) && (int) $requestData['status_id'] === Status::STATUS_DELETED) {
            $this->destroyUser($user);
        }

        return $user;
    }

    public function destroyUser(User $user): void
    {
        $user->delete();
    }

    public function blockUser(User $user): void
    {
        $user->setBlocked();
    }

    public function unblockUser(User $user): void
    {
        $user->setUnblocked();
    }

    public function updatePassword(User $user, array $requestData): void
    {
        $user->update([
            'password' => Hash::make($requestData['password']),
            'remember_token' => Str::random(60)
        ]);
        event(new PasswordReset($user));
    }

    public function updateRoles(User $user, array $requestData): void
    {
        $user->syncRoles($requestData['user_role'] ?? []);
    }

    public function updatePermissions(User $user, array $requestData): void
    {
        $user->syncPermissions($requestData['permissions'] ?? []);
    }

    public function confirmEmailUser(User $user): void
    {
        $user->update(['email_verified_at' => Carbon::now()]);
    }

    public function resetConfirmEmailUser(User $user): void
    {
        $user->update(['email_verified_at' => null]);
    }
}
