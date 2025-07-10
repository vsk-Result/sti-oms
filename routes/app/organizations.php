<?php

use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Organization\ExportController;
use App\Http\Controllers\Organization\TransferPaymentController;
use App\Http\Controllers\Organization\TransferPaymentImportController;
use App\Http\Controllers\Organization\WarningOrganizationController;

// Экспорт контрагентов

Route::post('organizations/export', [ExportController::class, 'store'])->name('organizations.exports.store');

// Обновление проблемных контрагентов

Route::post('organizations/warnings', [WarningOrganizationController::class, 'store'])->name('organizations.warnings.store');


// Импорт переноса оплат

Route::post('organizations/transfer-payments/import', [TransferPaymentImportController::class, 'store'])->name('organizations.transfer_payments.import.store')->middleware('can:edit organizations');
Route::post('organizations/transfer-payments/import/update', [TransferPaymentImportController::class, 'update'])->name('organizations.transfer_payments.import.update')->middleware('can:index scheduler');

// Контрагенты

Route::get('organizations', [OrganizationController::class, 'index'])->name('organizations.index')->middleware('can:index organizations');
Route::get('organizations/create', [OrganizationController::class, 'create'])->name('organizations.create')->middleware('can:create organizations');
Route::post('organizations', [OrganizationController::class, 'store'])->name('organizations.store')->middleware('can:create organizations');
Route::get('organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show')->middleware('can:show organizations');
Route::get('organizations/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit')->middleware('can:edit organizations');
Route::post('organizations/{organization}', [OrganizationController::class, 'update'])->name('organizations.update')->middleware('can:edit organizations');
Route::delete('organizations/{organization}', [OrganizationController::class, 'destroy'])->name('organizations.destroy')->middleware('can:edit organizations');

// Перенос оплат

Route::get('organizations/{organization}/transfer-payments/create', [TransferPaymentController::class, 'create'])->name('organizations.transfer_payments.create')->middleware('can:edit organizations');
Route::post('organizations/{organization}/transfer-payments', [TransferPaymentController::class, 'store'])->name('organizations.transfer_payments.store')->middleware('can:edit organizations');
