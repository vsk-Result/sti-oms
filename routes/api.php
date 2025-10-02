<?php

use App\Http\Controllers\API\BankGuarantee\BankGuaranteeController;
use App\Http\Controllers\API\BankGuarantee\ExportController as BExportController;
use App\Http\Controllers\API\Loan\LoanController;
use App\Http\Controllers\API\Object\AccessController;
use App\Http\Controllers\API\Object\WorkerSalaryController;
use App\Http\Controllers\API\Pivot\Act\ActController;
use App\Http\Controllers\API\Pivot\Act\ActNotPaidController;
use App\Http\Controllers\API\Pivot\Act\ExportController;
use App\Http\Controllers\API\Pivot\Debt\DebtController;
use App\Http\Controllers\API\Pivot\Debt\ExportController as DExportController;
use App\Http\Controllers\API\Pivot\MoneyMovement\InfoController;
use App\Http\Controllers\API\Pivot\MoneyMovement\ExportController as MMExportController;
use App\Http\Controllers\API\Pivot\Object\ObjectInfoController;
use App\Http\Controllers\API\Pivot\Object\CloseObjectDebtsController;
use App\Http\Controllers\API\Pivot\Object\PivotController;
use App\Http\Controllers\API\Pivot\Payment\PaymentController;
use App\Http\Controllers\API\Pivot\Bank\BankController;
use App\Http\Controllers\API\Object\DebtController as ODebtController;
use App\Http\Controllers\API\Object\DebtV2Controller;
use App\Http\Controllers\API\Object\BalanceController;
use App\Http\Controllers\API\Object\ManagerController;
use App\Http\Controllers\API\Debt\OrganizationController;
use App\Http\Controllers\API\Finance\FinanceReportExportController;
use App\Http\Controllers\API\Pivot\TaxPlan\TaxPlanExportController;
use App\Http\Controllers\API\Gromisoft\Employees\EmployeeController;
use App\Http\Controllers\API\Finance\GeneralReportController;
use App\Http\Controllers\API\Pivot\CashFlow\CashFlowController;
use App\Http\Controllers\API\Pivot\CashFlow\CashFlowExportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CashCheck\CashCheckController;
use App\Http\Controllers\API\CashAccount\CashAccountController;

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
Route::get('pivots/acts-not-paid', [ActNotPaidController::class, 'index']);
Route::get('pivots/acts/export', [ExportController::class, 'store']);

Route::get('pivots/debts', [DebtController::class, 'index']);
Route::get('pivots/debts/export', [DExportController::class, 'store']);

Route::get('pivots/payments', [PaymentController::class, 'index']);

Route::get('pivots/banks', [BankController::class, 'index']);

Route::get('pivots/objects/info', [ObjectInfoController::class, 'index']);
Route::get('pivots/objects/debts-for-close-objects', [CloseObjectDebtsController::class, 'index']);

Route::get('pivots/objects/pivot', [PivotController::class, 'index']);

Route::get('pivots/cash-flow', [CashFlowController::class, 'index']);
Route::get('pivots/cash-flow/export', [CashFlowExportController::class, 'index']);

Route::get('pivots/workers-salary', [WorkerSalaryController::class, 'index']);

Route::get('pivots/tax-plan/export', [TaxPlanExportController::class, 'index']);

Route::get('objects/access', [AccessController::class, 'index']);
Route::get('objects/debts', [ODebtController::class, 'index']);
Route::get('objects/debts-v2', [DebtV2Controller::class, 'index']);
Route::get('objects/balance', [BalanceController::class, 'index']);
Route::get('objects/managers', [ManagerController::class, 'index']);

Route::get('debts/organizations', [OrganizationController::class, 'index']);

Route::get('bank-guarantees', [BankGuaranteeController::class, 'index']);
Route::get('bank-guarantees/export', [BExportController::class, 'store']);

Route::get('loans', [LoanController::class, 'index']);

Route::get('exports/finance/finance-report', [FinanceReportExportController::class, 'store']);
Route::get('finance/general-report', [GeneralReportController::class, 'index']);

Route::post('v1/gromisoft/employees', [EmployeeController::class, 'index']);

Route::get('/crm-cash-check/create', [CashCheckController::class, 'store'])->name('crm_cash_check.store');

Route::get('pivots/money-movement/info', [InfoController::class, 'index']);
Route::get('pivots/money-movement/export', [MMExportController::class, 'store']);

Route::get('pivots/cash-accounts', [CashAccountController::class, 'index']);


