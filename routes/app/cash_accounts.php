<?php

use App\Http\Controllers\CashAccount\CashAccountController;
use App\Http\Controllers\CashAccount\RequestCashController;
use App\Http\Controllers\CashAccount\TransferCashController;
use App\Http\Controllers\CashAccount\Payment\PaymentController;
use App\Http\Controllers\CashAccount\Payment\ExportController;

// Запрос средств у другой кассы

Route::post('cash-accounts/{cashAccount}/request-cash', [RequestCashController::class, 'store'])->name('cash_accounts.request_cash.store');
Route::get('cash-accounts/{cashAccount}/payments/{payment}/request-cash/update', [RequestCashController::class, 'update'])->name('cash_accounts.request_cash.update');

// Передача средств другой кассе

Route::post('cash-accounts/{cashAccount}/transfer-cash', [TransferCashController::class, 'store'])->name('cash_accounts.transfer_cash.store');
Route::get('cash-accounts/{cashAccount}/payments/{payment}/transfer-cash/update', [TransferCashController::class, 'update'])->name('cash_accounts.transfer_cash.update');


// Кассы

Route::get('cash-accounts', [CashAccountController::class, 'index'])->name('cash_accounts.index')->middleware('can:index cash-accounts');
Route::get('cash-accounts/create', [CashAccountController::class, 'create'])->name('cash_accounts.create')->middleware('can:create cash-accounts');
Route::post('cash-accounts', [CashAccountController::class, 'store'])->name('cash_accounts.store')->middleware('can:create cash-accounts');
Route::get('cash-accounts/{cashAccount}', [CashAccountController::class, 'show'])->name('cash_accounts.show')->middleware('can:show cash-accounts');
Route::get('cash-accounts/{cashAccount}/edit', [CashAccountController::class, 'edit'])->name('cash_accounts.edit')->middleware('can:edit cash-accounts');
Route::post('cash-accounts/{cashAccount}', [CashAccountController::class, 'update'])->name('cash_accounts.update')->middleware('can:edit cash-accounts');
Route::delete('cash-accounts/{cashAccount}', [CashAccountController::class, 'destroy'])->name('cash_accounts.destroy')->middleware('can:edit cash-accounts');

// Экспорт оплат по кассам

Route::post('cash-accounts/{cashAccount}/payments/export', [ExportController::class, 'store'])->name('cash_accounts.payments.exports.store');

// Оплаты по кассам

Route::get('cash-accounts/{cashAccount}/payments', [PaymentController::class, 'index'])->name('cash_accounts.payments.index');
Route::get('cash-accounts/{cashAccount}/payments/create', [PaymentController::class, 'create'])->name('cash_accounts.payments.create');
Route::post('cash-accounts/{cashAccount}/payments', [PaymentController::class, 'store'])->name('cash_accounts.payments.store');
Route::get('cash-accounts/{cashAccount}/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('cash_accounts.payments.edit');
Route::post('cash-accounts/{cashAccount}/payments/{payment}', [PaymentController::class, 'update'])->name('cash_accounts.payments.update');
Route::delete('cash-accounts/{cashAccount}/payments/{payment}/destroy', [PaymentController::class, 'destroy'])->name('cash_accounts.payments.destroy');