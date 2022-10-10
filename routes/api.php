<?php

use App\Http\Controllers\API\Pivot\Act\ActController;
use App\Http\Controllers\API\Pivot\Act\ExportController;
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