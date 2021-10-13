<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Log\LogController;
use App\Http\Controllers\Statement\StatementController;
use App\Http\Controllers\Statement\PaymentSplitController;
use App\Http\Controllers\Statement\ExportController as StatementExportController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Object\ObjectController;
use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\CRM\AvansImportController;

use App\Http\Controllers\User\{
    UserController,
    BlockController,
    UnblockController,
    PasswordResetController,
    RoleController as UserRoleController,
    PermissionController,
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';

Route::group(['middleware' => ['auth', 'verified', 'active']], function () {

    // CRM
    Route::get('/crm/avanses/imports', [AvansImportController::class, 'index'])->name('crm.avanses.imports.index');

    // Общее
    Route::get('/', fn () => redirect()->route('objects.index'))->name('home');

    // Выписки
    Route::get('statements', [StatementController::class, 'index'])->name('statements.index')->middleware('can:index statements');
    Route::get('statements/create', [StatementController::class, 'create'])->name('statements.create')->middleware('can:create statements');
    Route::post('statements', [StatementController::class, 'store'])->name('statements.store')->middleware('can:create statements');
    Route::get('statements/{statement}', [StatementController::class, 'show'])->name('statements.show')->middleware('can:show statements');
    Route::get('statements/{statement}/edit', [StatementController::class, 'edit'])->name('statements.edit')->middleware('can:edit statements');
    Route::delete('statements/{statement}', [StatementController::class, 'destroy'])->name('statements.destroy')->middleware('can:edit statements');

    Route::post('statements/{statement}/exports', [StatementExportController::class, 'store'])->name('statements.exports.store')->middleware('can:show statements');
    Route::post('statements/{statement}/payments/{payment}/split', [PaymentSplitController::class, 'store'])->name('statements.payments.split.store')->middleware('can:edit statements');

    // Оплаты
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Объекты
    Route::get('objects', [ObjectController::class, 'index'])->name('objects.index');

    // Компании
    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index')->middleware('can:index companies');
    Route::get('companies/create', [CompanyController::class, 'create'])->name('companies.create')->middleware('can:create companies');
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store')->middleware('can:create companies');
    Route::get('companies/{company}', [CompanyController::class, 'show'])->name('companies.show')->middleware('can:show companies');
    Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit')->middleware('can:edit companies');
    Route::post('companies/{company}', [CompanyController::class, 'update'])->name('companies.update')->middleware('can:edit companies');
    Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy')->middleware('can:edit companies');

    // Организации
    Route::get('organizations', [OrganizationController::class, 'index'])->name('organizations.index')->middleware('can:index organizations');
    Route::get('organizations/create', [OrganizationController::class, 'create'])->name('organizations.create')->middleware('can:create organizations');
    Route::post('organizations', [OrganizationController::class, 'store'])->name('organizations.store')->middleware('can:create organizations');
    Route::get('organizations/{organization}', [OrganizationController::class, 'show'])->name('organizations.show')->middleware('can:show organizations');
    Route::get('organizations/{organization}/edit', [OrganizationController::class, 'edit'])->name('organizations.edit')->middleware('can:edit organizations');
    Route::post('organizations/{organization}', [OrganizationController::class, 'update'])->name('organizations.update')->middleware('can:edit organizations');
    Route::delete('organizations/{organization}', [OrganizationController::class, 'destroy'])->name('organizations.destroy')->middleware('can:edit organizations');

    // Пользователи
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('can:index admin-users');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('can:show admin-users');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:edit admin-users');

    Route::post('users/{user}/block', [BlockController::class, 'store'])->name('users.block')->middleware('can:edit admin-users');
    Route::post('users/{user}/unblock', [UnblockController::class, 'store'])->name('users.unblock')->middleware('can:edit admin-users');

    Route::get('users/{user}/passwords/reset', [PasswordResetController::class, 'edit'])->name('users.passwords.reset.edit');
    Route::post('users/{user}/passwords/reset', [PasswordResetController::class, 'update'])->name('users.passwords.reset.update');

    Route::post('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update')->middleware('can:edit admin-users');
    Route::post('users/{user}/permissions', [PermissionController::class, 'update'])->name('users.permissions.update')->middleware('can:edit admin-users');

    // Роли доступа
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('can:index admin-roles');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('can:create admin-roles');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('can:create admin-roles');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('can:edit admin-roles');
    Route::post('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('can:edit admin-roles');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('can:edit admin-roles');

    // Менеджер логов
    Route::get('logs', [LogController::class, 'index'])->name('logs.index')->middleware('can:index admin-logs');
    Route::get('logs/{log}', [LogController::class, 'show'])->name('logs.show')->middleware('can:show admin-logs');
    Route::post('logs/{log}', [LogController::class, 'update'])->name('logs.update')->middleware('can:show admin-logs');
});
