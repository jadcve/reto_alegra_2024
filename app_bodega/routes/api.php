<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

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


Route::post('/request-ingredients', [InventoryController::class, 'requestIngredients']);
Route::get('/get-ingrediens', [InventoryController::class, 'getIngredients']);
Route::get('/get-purchase-logs', [InventoryController::class, 'getPurchaseLogs']);

