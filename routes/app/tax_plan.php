<?php

use App\Http\Controllers\TaxPlanItem\TaxPlanItemController;
use App\Http\Controllers\TaxPlanItem\HistoryController;
use App\Http\Controllers\TaxPlanItem\ExportController;

// История оплат

Route::get('tax-plan/history', [HistoryController::class, 'index'])->name('tax_plan.history.index');

// Экспорт плана

Route::post('tax-plan/export', [ExportController::class, 'store'])->name('tax_plan.exports.store');

// План налогов к оплате

Route::get('tax-plan', [TaxPlanItemController::class, 'index'])->name('tax_plan.index')->middleware('can:index tax_plan');
Route::get('tax-plan/create', [TaxPlanItemController::class, 'create'])->name('tax_plan.create')->middleware('can:create tax_plan');
Route::post('tax-plan', [TaxPlanItemController::class, 'store'])->name('tax_plan.store')->middleware('can:create tax_plan');
Route::get('tax-plan/{item}/edit', [TaxPlanItemController::class, 'edit'])->name('tax_plan.edit')->middleware('can:edit tax_plan');
Route::post('tax-plan/{item}', [TaxPlanItemController::class, 'update'])->name('tax_plan.update')->middleware('can:edit tax_plan');
Route::delete('tax-plan/{item}', [TaxPlanItemController::class, 'destroy'])->name('tax_plan.destroy')->middleware('can:edit tax_plan');

