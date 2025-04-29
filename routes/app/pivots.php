<?php

use App\Http\Controllers\Pivot\Balance\BalanceController;
use App\Http\Controllers\Pivot\Debt\DebtController;
use App\Http\Controllers\Pivot\Debt\ExportController;
use App\Http\Controllers\Pivot\Act\ActController;
use App\Http\Controllers\Pivot\Act\ExportController as ActExportController;
use App\Http\Controllers\Pivot\DTSTI\DTSTIController;
use App\Http\Controllers\Pivot\DTSTI\ExportController as DTSTIExportController;
use App\Http\Controllers\Pivot\CashFlow\CashFlowController;
use App\Http\Controllers\Pivot\CashFlow\ExportController as CashFlowExportController;
use App\Http\Controllers\Pivot\CashFlow\PlanPaymentController;
use App\Http\Controllers\Pivot\CashFlow\PlanPaymentEntryController;
use App\Http\Controllers\Pivot\CashFlow\PlanPaymentTableController;
use App\Http\Controllers\Pivot\CashFlow\PlanPaymentGroupController;
use App\Http\Controllers\Pivot\CashFlow\NotificationController;
use App\Http\Controllers\Pivot\CashFlow\CommentController;
use App\Http\Controllers\Pivot\ActCategory\ActCategoryController;
use App\Http\Controllers\Pivot\ActCategory\ExportController as ActCategoryExportController;
use App\Http\Controllers\Pivot\MoneyMovement\MoneyMovementController;
use App\Http\Controllers\Pivot\MoneyMovement\ExportController as MoneyMovementExportController;
use App\Http\Controllers\Pivot\Residence\ResidenceController;
use App\Http\Controllers\Pivot\Residence\ResidenceExportController;

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

// Сводная по долгам ДТ - СТИ
Route::get('pivots/dt-sti', [DTSTIController::class, 'index'])->name('pivots.dtsti.index');

// Экспорт сводной по долгам ДТ - СТИ
Route::post('pivots/dt-sti/export', [DTSTIExportController::class, 'store'])->name('pivots.dtsti.exports.store');

// Отчет по CASH FLOW
Route::get('pivots/cash-flow', [CashFlowController::class, 'index'])->name('pivots.cash_flow.index');
Route::post('pivots/cash-flow/export', [CashFlowExportController::class, 'store'])->name('pivots.cash_flow.exports.store');

Route::post('pivots/cash-flow/plan-payments', [PlanPaymentController::class, 'store'])->name('pivots.cash_flow.plan_payments.store');
Route::post('pivots/cash-flow/plan-payments/update', [PlanPaymentController::class, 'update'])->name('pivots.cash_flow.plan_payments.update');
Route::post('pivots/cash-flow/plan-payments/destroy', [PlanPaymentController::class, 'destroy'])->name('pivots.cash_flow.plan_payments.destroy');
Route::post('pivots/cash-flow/plan-payments/entries', [PlanPaymentEntryController::class, 'store'])->name('pivots.cash_flow.plan_payments.entries.store');
Route::post('pivots/cash-flow/plan-payments/group/create', [PlanPaymentGroupController::class, 'store'])->name('pivots.cash_flow.plan_payments.group.store');
Route::post('pivots/cash-flow/plan-payments/group/update', [PlanPaymentGroupController::class, 'update'])->name('pivots.cash_flow.plan_payments.group.update');
Route::get('pivots/cash-flow/plan-payments/group/{group}/destroy', [PlanPaymentGroupController::class, 'destroy'])->name('pivots.cash_flow.plan_payments.group.destroy');
Route::get('pivots/cash-flow/plan-payments/table', [PlanPaymentTableController::class, 'index'])->name('pivots.cash_flow.plan_payments.table.index');
Route::get('pivots/cash-flow/notifications/read', [NotificationController::class, 'update'])->name('pivots.cash_flow.notifications.update');
Route::post('pivots/cash-flow/comments/update', [CommentController::class, 'update'])->name('pivots.cash_flow.comments.update');


// Отчет по категориям
Route::get('pivots/acts-category', [ActCategoryController::class, 'index'])->name('pivots.acts_category.index');
// Экспорт отчета по категориям
Route::post('pivots/acts-category/export', [ActCategoryExportController::class, 'store'])->name('pivots.acts_category.exports.store');

// Отчет о движении денежных средств
Route::get('pivots/money-movement', [MoneyMovementController::class, 'index'])->name('pivots.money_movement.index');
// Экспорт отчета о движении денежных средств
Route::post('pivots/money-movement/export', [MoneyMovementExportController::class, 'store'])->name('pivots.money_movement.exports.store');

// Отчет по проживанию
Route::get('pivots/residence', [ResidenceController::class, 'index'])->name('pivots.residence.index');
// Экспорт отчета по проживанию
Route::post('pivots/residence/export', [ResidenceExportController::class, 'store'])->name('pivots.residence.exports.store');
