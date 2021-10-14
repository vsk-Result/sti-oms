<?php

use App\Http\Controllers\Statement\StatementController;
use App\Http\Controllers\Statement\PaymentSplitController;
use App\Http\Controllers\Statement\ExportController;

// Выписки

Route::get('statements', [StatementController::class, 'index'])->name('statements.index')->middleware('can:index statements');
Route::get('statements/create', [StatementController::class, 'create'])->name('statements.create')->middleware('can:create statements');
Route::post('statements', [StatementController::class, 'store'])->name('statements.store')->middleware('can:create statements');
Route::get('statements/{statement}', [StatementController::class, 'show'])->name('statements.show')->middleware('can:show statements');
Route::get('statements/{statement}/edit', [StatementController::class, 'edit'])->name('statements.edit')->middleware('can:edit statements');
Route::delete('statements/{statement}', [StatementController::class, 'destroy'])->name('statements.destroy')->middleware('can:edit statements');

Route::post('statements/{statement}/exports', [ExportController::class, 'store'])->name('statements.exports.store')->middleware('can:show statements');
Route::post('statements/{statement}/payments/{payment}/split', [PaymentSplitController::class, 'store'])->name('statements.payments.split.store')->middleware('can:edit statements');
