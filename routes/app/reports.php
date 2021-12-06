<?php

use App\Http\Controllers\Reports\ITRObjectSalary\ITRObjectSalaryController;

// Отчеты

// Отчет по расходам на ЗП ИТР по проектам

Route::get('reports/itr-salary-object/create', [ITRObjectSalaryController::class, 'create'])->name('reports.itr_salary_object.create');
Route::post('reports/itr-salary-object', [ITRObjectSalaryController::class, 'store'])->name('reports.itr_salary_object.store');
