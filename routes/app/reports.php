<?php

use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Report\ITRObjectSalaryController;
use App\Http\Controllers\Report\PaymentNDSAnalyzeController;
use App\Http\Controllers\Report\UpdatePaymentController;
use App\Http\Controllers\Report\AllReportController;

// Отчеты
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

// Отчет по расходам на ЗП ИТР по проектам

Route::post('reports/itr-salary-object', [ITRObjectSalaryController::class, 'store'])->name('reports.itr_salary_object.store');

// Анализ оплат на предмет необходимости вычесть НДС

Route::post('reports/payment-nds-analyze', [PaymentNDSAnalyzeController::class, 'store'])->name('reports.payment_nds_analyze.store');

// Обновление записей оплат

Route::post('reports/update-payments', [UpdatePaymentController::class, 'store'])->name('reports.update_payments.store');

// Свод отчетов

Route::get('reports/all-reports', [AllReportController::class, 'index'])->name('reports.all_reports.index');
