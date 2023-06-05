<?php

use App\Http\Controllers\Finance\GeneralCostsController;
use App\Http\Controllers\Finance\ExportController;

// Распределение общих затрат

Route::get('general-costs', [GeneralCostsController::class, 'index'])->name('general_costs.index')->middleware('can:index general-costs');
Route::post('general-costs/export', [ExportController::class, 'store'])->name('general_costs.exports.store')->middleware('can:index general-costs');
