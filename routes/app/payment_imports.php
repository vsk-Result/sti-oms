<?php

use App\Http\Controllers\PaymentImport\ImportController;
use App\Http\Controllers\PaymentImport\ExportController;
use App\Http\Controllers\PaymentImport\Type\StatementImportController;
use App\Http\Controllers\PaymentImport\Type\CRMCostClosureImportController;
use App\Http\Controllers\PaymentImport\Type\HistoryImportController;

// Загрузка оплат

Route::get('payment-imports', [ImportController::class, 'index'])->name('payment_imports.index')->middleware('can:index payment-imports');
Route::get('payment-imports/{import}', [ImportController::class, 'show'])->name('payment_imports.show')->middleware('can:show payment-imports');
Route::get('payment-imports/{import}/edit', [ImportController::class, 'edit'])->name('payment_imports.edit')->middleware('can:edit payment-imports');
Route::delete('payment-imports/{import}', [ImportController::class, 'destroy'])->name('payment_imports.destroy')->middleware('can:edit payment-imports');

// Экспорт в Excel для разнесения в таблицы Dropbox

Route::post('payment-imports/{import}/exports', [ExportController::class, 'store'])->name('payment_imports.exports.store')->middleware('can:show payment-imports');

// Загрузка из выписки

Route::get('payment-imports/types/statements/create', [StatementImportController::class, 'create'])->name('payment_imports.types.statements.create')->middleware('can:create payment-imports');
Route::post('payment-imports/types/statements', [StatementImportController::class, 'store'])->name('payment_imports.types.statements.store')->middleware('can:create payment-imports');

// Загрузка из кассы CRM

Route::get('payment-imports/types/crm_cost_closures/create', [CRMCostClosureImportController::class, 'create'])->name('payment_imports.types.crm_cost_closures.create')->middleware('can:create payment-imports');
Route::post('payment-imports/types/crm_cost_closures', [CRMCostClosureImportController::class, 'store'])->name('payment_imports.types.crm_cost_closures.store')->middleware('can:create payment-imports');

// Загрузка из истории оплат

Route::get('payment-imports/types/history/create', [HistoryImportController::class, 'create'])->name('payment_imports.types.history.create')->middleware('can:create payment-imports');
Route::post('payment-imports/types/history', [HistoryImportController::class, 'store'])->name('payment_imports.types.history.store')->middleware('can:create payment-imports');
