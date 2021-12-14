<?php

use App\Http\Controllers\Contract\ActController;

// Акты

Route::get('acts', [ActController::class, 'index'])->name('acts.index')->middleware('can:index acts');
