<?php

use App\Http\Controllers\Object\ObjectController;

// Объекты

Route::get('objects', [ObjectController::class, 'index'])->name('objects.index');
