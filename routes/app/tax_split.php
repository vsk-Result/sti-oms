<?php

use App\Http\Controllers\TaxSplit\TaxSplitController;

// Разбивка налогов

Route::get('tax-split', [TaxSplitController::class, 'index'])->name('tax_split.index');
Route::post('tax-split', [TaxSplitController::class, 'store'])->name('tax_split.store');
