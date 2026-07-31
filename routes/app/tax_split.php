<?php

use App\Http\Controllers\TaxSplit\TaxSplitController;
use App\Http\Controllers\TaxSplit\SplitInfoController;

// Разбивка налогов

Route::get('tax-split', [TaxSplitController::class, 'index'])->name('tax_split.index');
Route::post('tax-split', [TaxSplitController::class, 'store'])->name('tax_split.store');

// Получение данных для разбивки из 1С

Route::get('tax-split/get-split-info', [SplitInfoController::class, 'index'])->name('tax_split.split_info.index');
