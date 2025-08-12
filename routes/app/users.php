<?php

use App\Http\Controllers\User\{
    UserController,
    BlockController,
    UnblockController,
    PasswordResetController,
    RoleController,
    PermissionController,
    EmailConfirmController,
    EmailConfirmResetController,
};

// Пользователи

Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('can:index admin-users');
Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('can:create admin-users');
Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('can:create admin-users');
Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('can:show admin-users');
Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::post('users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:edit admin-users');

// Блокировка пользователя

Route::post('users/{user}/block', [BlockController::class, 'store'])->name('users.block')->middleware('can:edit admin-users');
Route::post('users/{user}/unblock', [UnblockController::class, 'store'])->name('users.unblock')->middleware('can:edit admin-users');

// Подтверждение Email пользователя

Route::get('users/{user}/email-confirm', [EmailConfirmController::class, 'store'])->name('users.email_confirm')->middleware('can:edit admin-users');
Route::get('users/{user}/email-confirm-reset', [EmailConfirmResetController::class, 'store'])->name('users.email_confirm_reset')->middleware('can:edit admin-users');

// Сброс пароля

Route::get('users/{user}/passwords/reset', [PasswordResetController::class, 'edit'])->name('users.passwords.reset.edit');
Route::post('users/{user}/passwords/reset', [PasswordResetController::class, 'update'])->name('users.passwords.reset.update');

// Обновление прав и ролей доступа

Route::post('users/{user}/roles', [RoleController::class, 'update'])->name('users.roles.update')->middleware('can:edit admin-users');
Route::post('users/{user}/permissions', [PermissionController::class, 'update'])->name('users.permissions.update')->middleware('can:edit admin-users');

// Войти под пользователем в систему

Route::post('users/{user}/login', [UserController::class, 'login'])->name('users.login')->middleware('can:edit admin-users');
