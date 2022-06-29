<?php

use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\SplitController;
use App\Http\Controllers\Payment\SplitResidenceController;
use App\Http\Controllers\Payment\SplitInsuranceController;
use App\Http\Controllers\Payment\SplitNDFLController;
use App\Http\Controllers\Payment\ExportController;
use App\Http\Controllers\Payment\CopyController;
use App\Http\Controllers\Payment\HistoryController;
use App\Http\Controllers\Payment\ClearController;

// Очистка ПТИ пустых оплат

Route::post('payments/clear', [ClearController::class, 'store'])->name('payments.clear.store');

// Разбивка оплат

Route::post('payments/{payment}/split', [SplitController::class, 'store'])->name('payments.split.store');

// Разбивка оплат проживания

Route::post('payments/split_residence', [SplitResidenceController::class, 'store'])->name('payments.split_residence.store');

// Разбивка оплат страховых взносов

Route::post('payments/split_insurance', [SplitInsuranceController::class, 'store'])->name('payments.split_insurance.store');

// Разбивка оплат НДФЛ

Route::post('payments/split_ndfl', [SplitNDFLController::class, 'store'])->name('payments.split_ndfl.store');

// История оплат

Route::get('payments/history', [HistoryController::class, 'index'])->name('payments.history.index');

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

// Копия оплаты

Route::post('payments/{payment}/copy', [CopyController::class, 'store'])->name('payments.copy.store');


