<?php

use App\Http\Controllers\Debt\DebtManualController;

// Долг, указанный вручную

Route::post('debts/debt-manual/create', [DebtManualController::class, 'store'])->name('debts.manual.store')->middleware('can:index debt-imports');
Route::post('debts/debt-manual', [DebtManualController::class, 'update'])->name('debts.manual.update')->middleware('can:index debt-imports');
