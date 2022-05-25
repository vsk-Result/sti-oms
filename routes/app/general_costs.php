<?php

use App\Http\Controllers\Finance\GeneralCostsController;

// Распределение общих затрат

Route::get('general-costs', [GeneralCostsController::class, 'index'])->name('general_costs.index')->middleware('can:index general-costs');
