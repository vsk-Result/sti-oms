<?php

use App\Http\Controllers\Log\LogController;

// Менеджер логов

Route::get('logs', [LogController::class, 'index'])->name('logs.index')->middleware('can:index admin-logs');
Route::get('logs/{log}', [LogController::class, 'show'])->name('logs.show')->middleware('can:show admin-logs');
Route::post('logs/{log}', [LogController::class, 'update'])->name('logs.update')->middleware('can:show admin-logs');
