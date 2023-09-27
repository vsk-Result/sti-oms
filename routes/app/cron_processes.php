<?php

use App\Http\Controllers\CRONProcessController;

// Проверка выполнения фоновых процессов
Route::get('cron-processes', [CRONProcessController::class, 'index'])->name('cron_processes.index');
