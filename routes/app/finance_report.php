<?php

use App\Http\Controllers\Finance\FinanceReport\FinanceReportController;
use App\Http\Controllers\Finance\FinanceReport\ExportController;
use App\Http\Controllers\Finance\FinanceReport\FinanceReportHistoryController;

// Финансовый отчет
Route::get('finance-report', [FinanceReportController::class, 'index'])->name('finance_report.index')->middleware('can:index finance-report');

// Экспорт финансового отчета
Route::post('finance-report/export', [ExportController::class, 'store'])->name('finance_report.exports.store')->middleware('can:index finance-report');

// История Финансовых отчетов
Route::get('finance-report/history', [FinanceReportHistoryController::class, 'index'])->name('finance_report.history.index')->middleware('can:index finance-report');
