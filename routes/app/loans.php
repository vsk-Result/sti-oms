<?php

use App\Http\Controllers\Loan\LoanController;

// Займы / Кредиты

Route::get('loans', [LoanController::class, 'index'])->name('loans.index')->middleware('can:index loans');
Route::get('loans/create', [LoanController::class, 'create'])->name('loans.create')->middleware('can:create loans');
Route::post('loans', [LoanController::class, 'store'])->name('loans.store')->middleware('can:create loans');
Route::get('loans/{loan}/edit', [LoanController::class, 'edit'])->name('loans.edit')->middleware('can:edit loans');
Route::post('loans/{loan}', [LoanController::class, 'update'])->name('loans.update')->middleware('can:edit loans');
Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy')->middleware('can:edit loans');
