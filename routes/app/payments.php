<?php

use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\SplitController;
use App\Http\Controllers\Payment\ExportController;
use App\Http\Controllers\Payment\CopyController;

// Экспорт оплат

Route::post('payments/export', [ExportController::class, 'store'])->name('payments.exports.store');

// Оплаты

Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
Route::post('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

// Разбивка оплат

Route::post('payments/{payment}/split', [SplitController::class, 'store'])->name('payments.split.store');

// Копия оплаты

Route::post('payments/{payment}/copy', [CopyController::class, 'store'])->name('payments.copy.store');


