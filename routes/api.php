<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/getAll', [StockController::class, 'getAll']);
Route::get('/getItemsByBranch/{branch}', [StockController::class, 'getItemsByBranch'])->name('getItemsByBranch');
Route::get('/getUniqueBranches', [StockController::class, 'getUniqueBranches'])->name('getUniqueBranches');
//Route::get('/get_data_by_month', [StockController::class, 'get_data_by_month'])->name('get_data_by_month');
Route::post('/get_data', [StockController::class, 'getStock'])->name('getData');
Route::post('/get_data_by_month', [StockController::class, 'getDataForSelectedMonth'])->name('getDataForSelectedMonth');


