<?php

use App\Http\Controllers\Organization\OrganizationController;

// Организации

Route::get('organizations', [OrganizationController::class, 'index'])->name('organizations.index')->middleware('can:index organizations');
Route::get('organizations/create', [OrganizationController::class, 'create'])->name('organizations.create')->middleware('can:create organizations');
Route::post('organizations', [OrganizationController::class, 'store'])->name('organizations.store')->middleware('can:create organizations');
Route::get('organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show')->middleware('can:show organizations');
Route::get('organizations/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit')->middleware('can:edit organizations');
Route::post('organizations/{organization}', [OrganizationController::class, 'update'])->name('organizations.update')->middleware('can:edit organizations');
Route::delete('organizations/{organization}', [OrganizationController::class, 'destroy'])->name('organizations.destroy')->middleware('can:edit organizations');
