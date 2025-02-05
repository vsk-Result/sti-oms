<?php

use App\Http\Controllers\DebtImport\ImportController;
use App\Http\Controllers\DebtImport\ImportManualController;
use App\Http\Controllers\DebtImport\ImportManualReplaceController;

// Загрузка подрядчиков из долгов объекта
Route::post('debt-imports/manual', [ImportManualController::class, 'store'])->name('debt_imports.manual.store');

// Загрузка долгов вручную
Route::post('debt-imports/manual-replace', [ImportManualReplaceController::class, 'store'])->name('debt_imports.manual_replace.store');

// Загрузка долгов

Route::get('debt-imports', [ImportController::class, 'index'])->name('debt_imports.index')->middleware('can:index debt-imports');
Route::get('debt-imports/create', [ImportController::class, 'create'])->name('debt_imports.create')->middleware('can:create debt-imports');
Route::post('debt-imports', [ImportController::class, 'store'])->name('debt_imports.store')->middleware('can:create debt-imports');
Route::delete('debt-imports/{import}', [ImportController::class, 'destroy'])->name('debt_imports.destroy')->middleware('can:edit debt-imports');

