<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::options('{any}', function (Request $request) {
    return response()->json([], 200);
})->where('any', '.*');

Route::group(["middleware" => "apikey.validate"], function () {
    Route::post('/orders/create', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

});

Route::post('/orders/update-status', [OrderController::class, 'updateStatus']);
