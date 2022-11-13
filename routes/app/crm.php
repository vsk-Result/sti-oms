<?php

use App\Http\Controllers\CRM\AvansImportController;
use App\Http\Controllers\CRM\SplitAvansImportController;
use App\Http\Controllers\CRM\CostStatusController;

// CRM

Route::get('/crm/avanses/imports', [AvansImportController::class, 'index'])->name('crm.avanses.imports.index');

// Статус касс CRM

Route::get('crm-costs', [CostStatusController::class, 'index'])->name('crm_costs.index')->middleware('can:index crm-costs');

// Статус переноса оплат на карты из CRM

Route::get('crm-split-avans-imports', [SplitAvansImportController::class, 'index'])->name('crm.avanses.imports.split.index')->middleware('can:index crm-split-avans-imports');