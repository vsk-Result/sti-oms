<?php

use App\Http\Controllers\DebtImport\ImportController;

// Загрузка долгов

Route::get('debt-imports', [ImportController::class, 'index'])->name('debt_imports.index')->middleware('can:index debt-imports');
Route::get('debt-imports/create', [ImportController::class, 'create'])->name('debt_imports.create')->middleware('can:create debt-imports');
Route::post('debt-imports', [ImportController::class, 'store'])->name('debt_imports.store')->middleware('can:create debt-imports');
Route::get('debt-imports/{import}', [ImportController::class, 'show'])->name('debt_imports.show')->middleware('can:show debt-imports');
Route::delete('debt-imports/{import}', [ImportController::class, 'destroy'])->name('debt_imports.destroy')->middleware('can:edit debt-imports');
