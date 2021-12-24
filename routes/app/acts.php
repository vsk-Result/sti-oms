<?php

use App\Http\Controllers\Contract\ActController;

// Акты

Route::get('acts', [ActController::class, 'index'])->name('acts.index')->middleware('can:index acts');
Route::get('acts/create', [ActController::class, 'create'])->name('acts.create')->middleware('can:create acts');
Route::post('acts', [ActController::class, 'store'])->name('acts.store')->middleware('can:create acts');
Route::get('acts/{act}', [ActController::class, 'show'])->name('acts.show')->middleware('can:show acts');
Route::get('acts/{act}/edit', [ActController::class, 'edit'])->name('acts.edit')->middleware('can:edit acts');
Route::post('acts/{act}', [ActController::class, 'update'])->name('acts.update')->middleware('can:edit acts');
Route::delete('acts/{act}', [ActController::class, 'destroy'])->name('acts.destroy')->middleware('can:edit acts');
