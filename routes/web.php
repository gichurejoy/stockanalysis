<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;

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

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/', function () {
//     return view('chart');
// });

Route::get('/getStock', [StockController::class, 'index']);
Route::get('/get_data', [StockController::class, 'getStock'])->name('getData');

Route::get('admin', function () {
    return view('admin');
});

// Route::get('/get_data/{getData}', function (Request $request, string $getData) {
//     return 'User '.$getData;
// });

// Route::post('/get_data', [StockController::class, 'getStock']);
