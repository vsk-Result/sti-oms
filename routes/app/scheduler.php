<?php

use App\Http\Controllers\Scheduler\SchedulerController;

// Планировщик задач

Route::get('scheduler', [SchedulerController::class, 'index'])->name('scheduler.index')->middleware('can:index scheduler');
