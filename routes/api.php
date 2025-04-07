<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/signup', [AuthController::class, 'signUp']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('category', CategoryController::class);
Route::post('/add-category', [CategoryController::class, 'store']);
Route::put('/update-category/{id}', [CategoryController::class, 'update']);
Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy']);
Route::get('category/search/{keyword}', [CategoryController::class, 'search']);
Route::patch('/updatecategory/{id}', [CategoryController::class, 'updateonly']);

Route::apiResource('subcategories', SubcategoryController::class);
Route::post('/add-subcategories', [SubcategoryController::class, 'store']);
Route::put('/update-subcategories/{id}', [SubcategoryController::class, 'update']);
Route::delete('/delete-subcategories/{id}', [SubcategoryController::class, 'destroy']);
Route::get('subcategories/search/{keyword}', [SubcategoryController::class, 'search']);
Route::patch('/subcategories/{id}', [SubcategoryController::class, 'updateonly']);

Route::apiResource('supplier', SupplierController::class);
Route::post('/add-supplier', [SupplierController::class, 'store']);
// Product Route
Route::apiResource('product', ProductController::class);
Route::post('/add-product', [ProductController::class, 'store']);
Route::post('/update-product/{id}', [ProductController::class, 'update']);
Route::get('/get-product/{id}', [ProductController::class, 'show']);
Route::delete('/delete-product/{id}', [ProductController::class, 'destroy']);
Route::patch('/product/{id}', [ProductController::class, 'updateonly']);
Route::get('/products/search/{keyword}', [ProductController::class, 'search']);
Route::get('/products/subcategory/{subcategoryId}', [ProductController::class, 'getProductsBySubcategory']);


//earch Routr
Route::get('/search/category/{keyword}', [SearchController::class, 'searchByCategory']);
Route::get('/search/subcategory/{keyword}', [SearchController::class, 'searchBySubcategory']);
