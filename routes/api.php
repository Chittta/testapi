<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('categories', CategoryController::class);
Route::post('/add-categories', [CategoryController::class, 'store']);
Route::put('/update-categories/{id}', [CategoryController::class, 'update']);
Route::delete('/delete-categories/{id}', [CategoryController::class, 'destroy']);
Route::get('categories/search/{keyword}', [CategoryController::class, 'search']);

