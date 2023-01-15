<?php

use App\Http\Controllers\Pivot\Balance\BalanceController;
use App\Http\Controllers\Pivot\Debt\DebtController;
use App\Http\Controllers\Pivot\Debt\ExportController;
use App\Http\Controllers\Pivot\Act\ActController;
use App\Http\Controllers\Pivot\Act\ExportController as ActExportController;

// Сводная по долгам от СТИ
Route::get('pivots/debts', [DebtController::class, 'index'])->name('pivots.debts.index');

// Экспорт сводной по долгам от СТИ
Route::post('pivots/debts/export', [ExportController::class, 'store'])->name('pivots.debts.exports.store');

// Сводная по долгам к СТИ
Route::get('pivots/acts', [ActController::class, 'index'])->name('pivots.acts.index');

// Экспорт сводной по долгам к СТИ
Route::post('pivots/acts/export', [ActExportController::class, 'store'])->name('pivots.acts.exports.store');

// Сводная по балансам
Route::get('pivots/balances', [BalanceController::class, 'index'])->name('pivots.balances.index');
