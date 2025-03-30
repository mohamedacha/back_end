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

Route::apiResource('products', ProductController::class);
Route::apiResource('services', ServiceController::class);
Route::apiResource('users', UserController::class) ; //done
Route::post('users/login', [UserController::class , 'login']) ; //done

Route::middleware('auth:sanctum')->group(function(){
    
    Route::post('/users/logout', [UserController::class, 'logout']);
    Route::apiResource('users', UserController::class)->except(['store' , 'login']);
    Route::apiResource('products', ProductController::class)->except(['show' , 'index']);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('services', ServiceController::class)->except(['index' , 'show']);
    
});


Route::get('/orders/index/{id}', [OrderController::class, 'index']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
Route::post('/orders/{id}/confirm', [OrderController::class, 'confirmOrder']);

