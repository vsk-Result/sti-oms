<?php

use App\Http\Controllers\Contract\ContractController;

// Договора

Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index')->middleware('can:index contracts');
Route::get('contracts/create', [ContractController::class, 'create'])->name('contracts.create')->middleware('can:create contracts');
Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store')->middleware('can:create contracts');
Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show')->middleware('can:show contracts');
Route::get('contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit')->middleware('can:edit contracts');
Route::post('contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update')->middleware('can:edit contracts');
Route::delete('contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy')->middleware('can:edit contracts');
