<?php

use App\Http\Controllers\Company\CompanyController;

// Компании

Route::get('companies', [CompanyController::class, 'index'])->name('companies.index')->middleware('can:index companies');
Route::get('companies/create', [CompanyController::class, 'create'])->name('companies.create')->middleware('can:create companies');
Route::post('companies', [CompanyController::class, 'store'])->name('companies.store')->middleware('can:create companies');
Route::get('companies/{company}', [CompanyController::class, 'show'])->name('companies.show')->middleware('can:show companies');
Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit')->middleware('can:edit companies');
Route::post('companies/{company}', [CompanyController::class, 'update'])->name('companies.update')->middleware('can:edit companies');
Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy')->middleware('can:edit companies');
