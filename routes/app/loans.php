<?php

use App\Http\Controllers\Loan\LoanController;
use App\Http\Controllers\Loan\LoanHistoryController;

// Займы / Кредиты

Route::get('loans', [LoanController::class, 'index'])->name('loans.index')->middleware('can:index loans');
Route::get('loans/create', [LoanController::class, 'create'])->name('loans.create')->middleware('can:create loans');
Route::post('loans', [LoanController::class, 'store'])->name('loans.store')->middleware('can:create loans');
Route::get('loans/{loan}/edit', [LoanController::class, 'edit'])->name('loans.edit')->middleware('can:edit loans');
Route::post('loans/{loan}', [LoanController::class, 'update'])->name('loans.update')->middleware('can:edit loans');
Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy')->middleware('can:edit loans');

// История Займов / Кредитов

Route::get('loans/{loan}/history', [LoanHistoryController::class, 'index'])->name('loans.history.index')->middleware('can:index loans');
Route::get('loans/{loan}/history/create', [LoanHistoryController::class, 'create'])->name('loans.history.create')->middleware('can:create loans');
Route::post('loans/{loan}/history', [LoanHistoryController::class, 'store'])->name('loans.history.store')->middleware('can:create loans');
Route::get('loans/{loan}/history/{history}/edit', [LoanHistoryController::class, 'edit'])->name('loans.history.edit')->middleware('can:edit loans');
Route::post('loans/{loan}/history/{history}', [LoanHistoryController::class, 'update'])->name('loans.history.update')->middleware('can:edit loans');
Route::delete('loans/{loan}/history/{history}', [LoanHistoryController::class, 'destroy'])->name('loans.history.destroy')->middleware('can:edit loans');

// Обновление Истории Займов / Кредитов

Route::get('loans/{loan}/history/reload', [LoanHistoryController::class, 'reload'])->name('loans.history.reload')->middleware('can:edit loans');