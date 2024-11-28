<?php

use App\Http\Controllers\CashCheck\CashCheckController;
use App\Http\Controllers\CashCheck\ManagerCheckController;
use App\Http\Controllers\CashCheck\ManagerUncheckController;

// Проверка касс CRM

Route::get('/crm-cash-check', [CashCheckController::class, 'index'])->name('crm_cash_check.index');
Route::get('/crm-cash-check/create', [CashCheckController::class, 'store'])->name('crm_cash_check.store');
Route::get('/crm-cash-check/{check}/show', [CashCheckController::class, 'show'])->name('crm_cash_check.show');

// Отметка менеджеров о прочтении

Route::get('/crm-cash-check/managers/{manager}/check', [ManagerCheckController::class, 'index'])->name('crm_cash_check.manager.check');
Route::get('/crm-cash-check/managers/{manager}/uncheck', [ManagerUncheckController::class, 'index'])->name('crm_cash_check.manager.uncheck');