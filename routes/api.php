<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;

Route::post('/signup', [AuthController::class, 'signUp']);
Route::post('/login', [AuthController::class, 'loginUser']);

Route::middleware('auth:sanctum')->group(function () {
    // category
    Route::post('/add-category', [CategoryController::class, 'store']);
    Route::put('/update-category/{id}', [CategoryController::class, 'update']);
    Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy']);
    Route::patch('/updatecategory/{id}', [CategoryController::class, 'updateonly']);
    // subcategories
    Route::post('/add-subcategories', [SubcategoryController::class, 'store']);
    Route::put('/update-subcategories/{id}', [SubcategoryController::class, 'update']);
    Route::delete('/delete-subcategories/{id}', [SubcategoryController::class, 'destroy']);
    Route::patch('/subcategories/{id}', [SubcategoryController::class, 'updateonly']);
    // product
    Route::post('/add-product', [ProductController::class, 'store']);
    Route::post('/update-product/{id}', [ProductController::class, 'update']);
    Route::delete('/delete-product/{id}', [ProductController::class, 'destroy']);
    Route::patch('/product/{id}', [ProductController::class, 'updateonly']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
// category Search
Route::apiResource('category', CategoryController::class);
Route::get('get-category/{id}', [CategoryController::class, 'show']);
Route::get('category/search/{keyword}', [CategoryController::class, 'search']);
// subcategories search
Route::apiResource('subcategories', SubcategoryController::class);
Route::get('subcategories/search/{keyword}', [SubcategoryController::class, 'search']);

// supplier
Route::apiResource('supplier', SupplierController::class);
Route::post('/add-supplier', [SupplierController::class, 'store']);
Route::put('/update-supplier/{id}', [SupplierController::class, 'update']);

// Product Route
Route::apiResource('product', ProductController::class);
Route::get('/get-product/{id}', [ProductController::class, 'show']);
Route::get('/products/search/{keyword}', [ProductController::class, 'search']);
Route::get('/products/subcategory/{subcategoryId}', [ProductController::class, 'getProductsBySubcategory']);

//search Routr
Route::get('/search/category/{keyword}', [SearchController::class, 'searchByCategory']);
Route::get('/search/subcategory/{keyword}', [SearchController::class, 'searchBySubcategory']);
Route::get('/search/categorysub/{id}', [SearchController::class, 'searchByCategoryId']);
