<?php

use App\Http\Controllers\AccruedTax\AccruedTaxController;

// Начисленные налоги

Route::get('accrued-taxes', [AccruedTaxController::class, 'index'])->name('accrued_taxes.index');
Route::post('accrued-taxes', [AccruedTaxController::class, 'update'])->name('accrued_taxes.update');
//Route::post('accrued-taxes/export', [AccruedTaxControllerExportController::class, 'store'])->name('accrued_taxes.exports.store');