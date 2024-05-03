<?php

use App\Http\Controllers\Finance\DistributionTransferService\DistributionTransferServiceController;
use App\Http\Controllers\Finance\DistributionTransferService\ExportController;

// Распределение услуг по трансферу

Route::get('distribution-transfer-service', [DistributionTransferServiceController::class, 'index'])->name('distribution_transfer_service.index')->middleware('can:index distribution-transfer-service');
Route::post('distribution-transfer-service/export', [ExportController::class, 'store'])->name('distribution_transfer_service.exports.store')->middleware('can:index distribution-transfer-service');
