<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

// Rutas protegidas por middleware de API Key
Route::group(["middleware" => "apikey.validate"], function () {
    Route::post('/orders/create', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/update-status', [OrderController::class, 'updateStatus']);
});

// Ruta para obtener ingredientes
Route::get('/ingredients', [OrderController::class, 'getIngredients']);

// Ruta de prueba para Angular
Route::get('/testAngular', [OrderController::class, 'testAngular']);

// Manejo de solicitudes OPTIONS
Route::options('/{any}', function() {
    return response()->json([], 200);
})->where('any', '.*');
