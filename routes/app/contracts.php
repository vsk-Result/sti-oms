<?php

use App\Http\Controllers\Contract\ContractController;
use App\Http\Controllers\Contract\ImportController;
use App\Http\Controllers\Contract\SubContractController;
use App\Http\Controllers\Contract\ActController;
use App\Http\Controllers\Contract\ExportController;

// Загрузка договоров

Route::post('contracts/import', [ImportController::class, 'store'])->name('contracts.import.store')->middleware('can:create contracts');
// Экспорт банковских гарантий и депозитов

Route::post('contracts/export', [ExportController::class, 'store'])->name('contracts.exports.store');


// Договора

Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index')->middleware('can:index contracts');
Route::get('contracts/create', [ContractController::class, 'create'])->name('contracts.create')->middleware('can:create contracts');
Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store')->middleware('can:create contracts');
Route::get('contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit')->middleware('can:edit contracts');
Route::post('contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update')->middleware('can:edit contracts');
Route::delete('contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy')->middleware('can:edit contracts');

// Дочерние договора

Route::get('contracts/{contract}/subcontracts', [SubContractController::class, 'index'])->name('contracts.subcontracts.index');

// Акты

Route::get('contracts/{contract}/acts', [ActController::class, 'index'])->name('contracts.acts.index');


