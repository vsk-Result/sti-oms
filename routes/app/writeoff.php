<?php

use App\Http\Controllers\Writeoff\WriteoffController;
use App\Http\Controllers\Writeoff\ExportController;
use App\Http\Controllers\Writeoff\HistoryController;

// История списаний

Route::get('writeoffs/history', [HistoryController::class, 'index'])->name('writeoffs.history.index');

// Экспорт списаний

Route::post('writeoffs/export', [ExportController::class, 'store'])->name('writeoffs.exports.store');

// Списания

Route::get('writeoffs', [WriteoffController::class, 'index'])->name('writeoffs.index');
Route::get('writeoffs/create', [WriteoffController::class, 'create'])->name('writeoffs.create');
Route::post('writeoffs', [WriteoffController::class, 'store'])->name('writeoffs.store');
Route::get('writeoffs/{writeoff}', [WriteoffController::class, 'show'])->name('writeoffs.show');
Route::post('writeoffs/{writeoff}', [WriteoffController::class, 'update'])->name('writeoffs.update');
Route::get('writeoffs/{writeoff}/edit', [WriteoffController::class, 'edit'])->name('writeoffs.edit');
Route::delete('writeoffs/{writeoff}', [WriteoffController::class, 'destroy'])->name('writeoffs.destroy');

