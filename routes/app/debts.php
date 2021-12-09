<?php

use App\Http\Controllers\Debt\DebtController;

// Долги

Route::get('debts', [DebtController::class, 'index'])->name('debts.index')->middleware('can:index debts');
Route::delete('debts/{import}', [DebtController::class, 'destroy'])->name('debts.destroy')->middleware('can:edit debts');
