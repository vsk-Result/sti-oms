<?php

use App\Http\Controllers\Bank\BankController;

// Банки

Route::get('banks', [BankController::class, 'index'])->name('banks.index')->middleware('can:index banks');
Route::get('banks/create', [BankController::class, 'create'])->name('banks.create')->middleware('can:create banks');
Route::post('banks', [BankController::class, 'store'])->name('banks.store')->middleware('can:create banks');
Route::get('banks/{bank}/edit', [BankController::class, 'edit'])->name('banks.edit')->middleware('can:edit banks');
Route::post('banks/{bank}', [BankController::class, 'update'])->name('banks.update')->middleware('can:edit banks');
Route::delete('banks/{bank}', [BankController::class, 'destroy'])->name('banks.destroy')->middleware('can:edit banks');
