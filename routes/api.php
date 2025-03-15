<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('orders', OrderController::class);
Route::apiResource('services', ServiceController::class);

<<<<<<< HEAD
// ------------
// Route::match(['put', 'patch'], '/api/users/{id}', [UserController::class, 'update']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::patch('/users/{id}', [UserController::class, 'update']);
});
=======


Route::get('/orders', [OrderController::class, 'index']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
Route::post('/orders/{id}/confirm', [OrderController::class, 'confirm']);
>>>>>>> c10150fbe8a1e424f81f56f866812f00e9e1c0dc
