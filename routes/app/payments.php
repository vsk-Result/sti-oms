<?php

use App\Http\Controllers\Payment\PaymentController;

// Оплаты

Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
Route::post('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
