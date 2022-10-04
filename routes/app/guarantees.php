<?php

use App\Http\Controllers\Guarantee\GuaranteeController;

// Гарантийные удержания

Route::get('guarantees', [GuaranteeController::class, 'index'])->name('guarantees.index')->middleware('can:index guarantees');
Route::get('guarantees/create', [GuaranteeController::class, 'create'])->name('guarantees.create')->middleware('can:create guarantees');
Route::post('guarantees', [GuaranteeController::class, 'store'])->name('guarantees.store')->middleware('can:create guarantees');
Route::get('guarantees/{guarantee}', [GuaranteeController::class, 'show'])->name('guarantees.show')->middleware('can:show guarantees');
Route::get('guarantees/{guarantee}/edit', [GuaranteeController::class, 'edit'])->name('guarantees.edit')->middleware('can:edit guarantees');
Route::post('guarantees/{guarantee}', [GuaranteeController::class, 'update'])->name('guarantees.update')->middleware('can:edit guarantees');
Route::delete('guarantees/{guarantee}', [GuaranteeController::class, 'destroy'])->name('guarantees.destroy')->middleware('can:edit guarantees');
