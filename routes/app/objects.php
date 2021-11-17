<?php

use App\Http\Controllers\Object\ObjectController;
use App\Http\Controllers\Object\PivotController;

// Объекты

Route::get('objects', [ObjectController::class, 'index'])->name('objects.index');
Route::get('objects/create', [ObjectController::class, 'create'])->name('objects.create');
Route::post('objects', [ObjectController::class, 'store'])->name('objects.store');
Route::get('objects/{object}', [ObjectController::class, 'show'])->name('objects.show');
Route::get('objects/{object}/edit', [ObjectController::class, 'edit'])->name('objects.edit');
Route::post('objects/{object}', [ObjectController::class, 'update'])->name('objects.update');

// Сводная информация

Route::get('objects/{object}/pivot', [PivotController::class, 'index'])->name('objects.pivot.index');
