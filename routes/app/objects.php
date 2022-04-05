<?php

use App\Http\Controllers\Object\ObjectController;
use App\Http\Controllers\Object\PivotController;
use App\Http\Controllers\Object\ContractController;
use App\Http\Controllers\Object\ActController;
use App\Http\Controllers\Object\GuaranteeController;
use App\Http\Controllers\Object\DebtController;
use App\Http\Controllers\Object\PaymentController;
use App\Http\Controllers\Object\CashPaymentController;
use App\Http\Controllers\Object\FileController;
use App\Http\Controllers\Object\ActivityController;
use App\Http\Controllers\Object\UserController;
use App\Http\Controllers\Object\BankGuaranteeController;

// Объекты

Route::get('objects', [ObjectController::class, 'index'])->name('objects.index');
Route::get('objects/create', [ObjectController::class, 'create'])->name('objects.create');
Route::post('objects', [ObjectController::class, 'store'])->name('objects.store');
Route::get('objects/{object}', [ObjectController::class, 'show'])->name('objects.show');
Route::get('objects/{object}/edit', [ObjectController::class, 'edit'])->name('objects.edit');
Route::post('objects/{object}', [ObjectController::class, 'update'])->name('objects.update');

// Сводная информация

Route::get('objects/{object}/pivot', [PivotController::class, 'index'])->name('objects.pivot.index');

// Договора

Route::get('objects/{object}/contracts', [ContractController::class, 'index'])->name('objects.contracts.index');

// Акты

Route::get('objects/{object}/acts', [ActController::class, 'index'])->name('objects.acts.index');

// Банковские гарантии

Route::get('objects/{object}/bank-guarantees', [BankGuaranteeController::class, 'index'])->name('objects.bank_guarantees.index');

// Гарантийные удержания

Route::get('objects/{object}/guarantees', [GuaranteeController::class, 'index'])->name('objects.guarantees.index');

// Долги

Route::get('objects/{object}/debts', [DebtController::class, 'index'])->name('objects.debts.index');

// Оплаты (безнал)

Route::get('objects/{object}/payments', [PaymentController::class, 'index'])->name('objects.payments.index');

// Касса

Route::get('objects/{object}/cash-payments', [CashPaymentController::class, 'index'])->name('objects.cash_payments.index');

// Файлы

Route::get('objects/{object}/files', [FileController::class, 'index'])->name('objects.files.index');

// Активность

Route::get('objects/{object}/activity', [ActivityController::class, 'index'])->name('objects.activity.index');

// Доступ к объектам

Route::get('objects-users', [UserController::class, 'index'])->name('objects.users.index')->middleware('can:index admin-roles');
Route::get('objects-users/{object}/edit', [UserController::class, 'edit'])->name('objects.users.edit')->middleware('can:edit admin-roles');
Route::post('objects-users/{object}', [UserController::class, 'update'])->name('objects.users.update')->middleware('can:edit admin-roles');


