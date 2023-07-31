<?php

use App\Http\Controllers\API\BankGuarantee\BankGuaranteeController;
use App\Http\Controllers\API\BankGuarantee\ExportController as BExportController;
use App\Http\Controllers\API\Loan\LoanController;
use App\Http\Controllers\API\Pivot\Act\ActController;
use App\Http\Controllers\API\Pivot\Act\ExportController;
use App\Http\Controllers\API\Pivot\Debt\DebtController;
use App\Http\Controllers\API\Pivot\Debt\ExportController as DExportController;
use App\Http\Controllers\API\Pivot\Object\ObjectInfoController;
use App\Http\Controllers\API\Pivot\Payment\PaymentController;
use App\Http\Controllers\API\Pivot\Bank\BankController;
use App\Http\Controllers\API\Object\DebtController as ODebtController;
use App\Http\Controllers\API\Debt\OrganizationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('pivots/acts', [ActController::class, 'index']);
Route::get('pivots/acts/export', [ExportController::class, 'store']);

Route::get('pivots/debts', [DebtController::class, 'index']);
Route::get('pivots/debts/export', [DExportController::class, 'store']);

Route::get('pivots/payments', [PaymentController::class, 'index']);

Route::get('pivots/banks', [BankController::class, 'index']);

Route::get('pivots/objects/info', [ObjectInfoController::class, 'index']);

Route::get('objects/debts', [ODebtController::class, 'index']);

Route::get('debts/organizations', [OrganizationController::class, 'index']);

Route::get('bank-guarantees', [BankGuaranteeController::class, 'index']);
Route::get('bank-guarantees/export', [BExportController::class, 'store']);

Route::get('loans', [LoanController::class, 'index']);

