<?php

use App\Http\Controllers\Role\RoleController;

// Роли доступа

Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('can:index admin-roles');
Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('can:create admin-roles');
Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('can:create admin-roles');
Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('can:edit admin-roles');
Route::post('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('can:edit admin-roles');
Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('can:edit admin-roles');
