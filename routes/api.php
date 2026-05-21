<?php

use App\Http\Controllers\Api\ProductsControllerApi;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('loginapi', [ProductsControllerApi::class, 'loginapi']);
Route::get('products', [ProductsControllerApi::class, 'index'])->middleware('auth:sanctum');
Route::post('products', [ProductsControllerApi::class, 'store'])->middleware('auth:sanctum');
Route::put('products/{id}', [ProductsControllerApi::class, 'update'])->middleware('auth:sanctum');
Route::delete('products/{id}', [ProductsControllerApi::class, 'destroy'])->middleware('auth:sanctum');
