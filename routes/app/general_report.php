<?php

use App\Http\Controllers\Finance\GeneralReport\GeneralReportController;
use App\Http\Controllers\Finance\GeneralReport\ExportController;

// Отчет по общим затратам
Route::get('general-report', [GeneralReportController::class, 'index'])->name('general_report.index')->middleware('can:index finance-report');

// Экспорт отчета по общим затратам
Route::post('general-report/export', [ExportController::class, 'store'])->name('general_report.exports.store')->middleware('can:index finance-report');
