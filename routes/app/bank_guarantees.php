<?php

use App\Http\Controllers\BankGuarantee\BankGuaranteeController;
use App\Http\Controllers\BankGuarantee\ImportController;
use App\Http\Controllers\BankGuarantee\ExportController;
use App\Http\Controllers\BankGuarantee\HistoryController;

// История

Route::get('bank-guarantees/history', [HistoryController::class, 'index'])->name('bank_guarantees.history.index');

// Экспорт банковских гарантий и депозитов

Route::post('bank-guarantees/export', [ExportController::class, 'store'])->name('bank_guarantees.exports.store');

// Загрузка договоров

Route::post('bank-guarantees/import', [ImportController::class, 'store'])->name('bank_guarantees.import.store')->middleware('can:create bank_guarantees');


// Банковские гарантии и депозиты

Route::get('bank-guarantees', [BankGuaranteeController::class, 'index'])->name('bank_guarantees.index')->middleware('can:index bank-guarantees');
Route::get('bank-guarantees/create', [BankGuaranteeController::class, 'create'])->name('bank_guarantees.create')->middleware('can:create bank-guarantees');
Route::post('bank-guarantees', [BankGuaranteeController::class, 'store'])->name('bank_guarantees.store')->middleware('can:create bank-guarantees');
Route::get('bank-guarantees/{guarantee}', [BankGuaranteeController::class, 'show'])->name('bank_guarantees.show')->middleware('can:show bank-guarantees');
Route::get('bank-guarantees/{guarantee}/edit', [BankGuaranteeController::class, 'edit'])->name('bank_guarantees.edit')->middleware('can:edit bank-guarantees');
Route::post('bank-guarantees/{guarantee}', [BankGuaranteeController::class, 'update'])->name('bank_guarantees.update')->middleware('can:edit bank-guarantees');
Route::delete('bank-guarantees/{guarantee}', [BankGuaranteeController::class, 'destroy'])->name('bank_guarantees.destroy')->middleware('can:edit bank-guarantees');
