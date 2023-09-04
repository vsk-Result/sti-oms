<?php

use App\Http\Controllers\Deposit\DepositController;
use App\Http\Controllers\Deposit\ImportController;
use App\Http\Controllers\Deposit\ExportController;
use App\Http\Controllers\Deposit\HistoryController;

// История

Route::get('deposits/history', [HistoryController::class, 'index'])->name('deposits.history.index');

// Экспорт депозитов

Route::post('deposits/export', [ExportController::class, 'store'])->name('deposits.exports.store');

// Депозиты

Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index')->middleware('can:index deposits');
Route::get('deposits/create', [DepositController::class, 'create'])->name('deposits.create')->middleware('can:create deposits');
Route::post('deposits', [DepositController::class, 'store'])->name('deposits.store')->middleware('can:create deposits');
Route::get('deposits/{deposit}', [DepositController::class, 'show'])->name('deposits.show')->middleware('can:show deposits');
Route::get('deposits/{deposit}/edit', [DepositController::class, 'edit'])->name('deposits.edit')->middleware('can:edit deposits');
Route::post('deposits/{deposit}', [DepositController::class, 'update'])->name('deposits.update')->middleware('can:edit deposits');
Route::delete('deposits/{deposit}', [DepositController::class, 'destroy'])->name('deposits.destroy')->middleware('can:edit deposits');
