<?php

use App\Http\Controllers\Pivot\DebtController;
use App\Http\Controllers\Pivot\ActController;

// Сводная по долгам от СТИ
Route::get('pivots/debts', [DebtController::class, 'index'])->name('pivots.debts.index');

// Сводная по долгам к СТИ
Route::get('pivots/acts', [ActController::class, 'index'])->name('pivots.acts.index');
