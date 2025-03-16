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

//------------------
// Route::post('/users/{id}', [UserController::class , 'update']); // ✅ Allow POST for PUT
// Route::put('/users/{id}', [UserController::class , 'update']); // ✅ Direct PUT
//------------------

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::patch('/users/{id}', [UserController::class, 'update']);
// });


Route::get('/orders', [OrderController::class, 'index']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
Route::post('/orders/{id}/confirm', [OrderController::class, 'confirmOrder']);
