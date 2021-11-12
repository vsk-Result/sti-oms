<?php

use App\Http\Controllers\Object\ObjectController;

// Объекты

Route::get('objects', [ObjectController::class, 'index'])->name('objects.index');
Route::get('objects/create', [ObjectController::class, 'create'])->name('objects.create');
Route::post('objects', [ObjectController::class, 'store'])->name('objects.store');
Route::get('objects/{object}', [ObjectController::class, 'show'])->name('objects.show');
