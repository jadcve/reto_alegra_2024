<?php

use Illuminate\Http\Request;
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



Route::get("/test", function () {
    return response()->json(["message" => "Hello, World!"]);
});

Route::group(["middleware" => "apikey.validate"], function () {
    Route::get("/test_api_key", function () {
        return response()->json(["message" => "Hello, World 2!"]);
    });
});
