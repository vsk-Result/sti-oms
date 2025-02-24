<?php

use App\Http\Controllers\CashCheck\CashCheckController;
use App\Http\Controllers\CashCheck\CashCheckDetailsExportController;
use App\Http\Controllers\CashCheck\ManagerCheckController;
use App\Http\Controllers\CashCheck\ManagerUncheckController;
use App\Http\Controllers\CashCheck\ManagerResetController;
use App\Http\Controllers\CashCheck\ManagerRevisionController;

// Проверка касс CRM

Route::get('/crm-cash-check', [CashCheckController::class, 'index'])->name('crm_cash_check.index');
//Route::get('/crm-cash-check/create', [CashCheckController::class, 'store'])->name('crm_cash_check.store');
Route::get('/crm-cash-check/{check}/show', [CashCheckController::class, 'show'])->name('crm_cash_check.show');
Route::get('/crm-cash-check/{check}/reset', [ManagerResetController::class, 'store'])->name('crm_cash_check.manager.reset');

// Отметка менеджеров о прочтении

Route::get('/crm-cash-check/managers/{manager}/check', [ManagerCheckController::class, 'index'])->name('crm_cash_check.manager.check');
Route::get('/crm-cash-check/managers/{manager}/uncheck', [ManagerUncheckController::class, 'index'])->name('crm_cash_check.manager.uncheck');
Route::get('/crm-cash-check/managers/{manager}/revision', [ManagerRevisionController::class, 'index'])->name('crm_cash_check.manager.revision');

// Экспорт детализации кассы к закрытию
Route::post('/crm-cash-check/{check}/export', [CashCheckDetailsExportController::class, 'store'])->name('crm_cash_check.exports.store');
