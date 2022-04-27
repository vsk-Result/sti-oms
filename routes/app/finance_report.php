<?php

use App\Http\Controllers\Finance\FinanceReportController;

// Финансовый отчет

Route::get('finance-report', [FinanceReportController::class, 'index'])->name('finance_report.index')->middleware('can:index finance-report');
