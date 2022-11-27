<?php

use App\Http\Controllers\Debt\DebtManualController;

// Долг, указанный вручную

Route::post('debts/debt-manual', [DebtManualController::class, 'update'])->name('debts.manual.update')->middleware('can:index debt-imports');
