<?php

use App\Http\Controllers\CRM\AvansImportController;

// CRM

Route::get('/crm/avanses/imports', [AvansImportController::class, 'index'])->name('crm.avanses.imports.index');
