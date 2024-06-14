<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;


Route::get('/orders-create', [OrderController::class, 'create'])->name('orders.create');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

Route::get('/ingredientes-bodega', [OrderController::class, 'showIngredients'])->name('store.ingredients');
Route::get('/menu-ingredients', [OrderController::class, 'getMenus' ])->name('menus.menuIngredients');
Route::get('/purchase-logs', [OrderController::class, 'getPurchaseOrders'])->name('purchase-log.purchase');
