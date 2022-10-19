<?php

use App\Http\Controllers\CRMCostStatusController;

// Статус касс CRM

Route::get('crm-costs', [CRMCostStatusController::class, 'index'])->name('crm_costs.index')->middleware('can:index crm-costs');