<?php

use App\Http\Controllers\UploadDebtStatus\UploadDebtStatusController;

// Статус загрузок долгов из сервера

Route::get('upload-debts-status', [UploadDebtStatusController::class, 'index'])->name('upload_debts_status.index');
Route::post('upload-debts-status', [UploadDebtStatusController::class, 'store'])->name('upload_debts_status.store');

