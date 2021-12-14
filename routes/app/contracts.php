<?php

use App\Http\Controllers\Contract\ContractController;

// Договора

Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index')->middleware('can:index contracts');
