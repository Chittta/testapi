<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;

class SearchController extends Controller
{
    public function searchByCategory($keyword)
    {
        $category = Category::where('CategoryName', 'LIKE', "%{$keyword}%")
            ->with([
                'subcategories',
                'products' => function ($query) {
                    $query->with('supplier');
                }
            ])
            ->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        return response()->json([
            'message' => 'Category search result',
            'data' => [
                'Category' => $category->only(['CategoryID', 'CategoryName', 'CategoryDescription']),
                'Subcategories' => $category->subcategories,
                'Products' => $category->products->map(function ($product) {
                    return [
                        'ProductId' => $product->ProductId,
                        'ProductName' => $product->ProductName,
                        'SupplierName' => $product->supplier->SupplierName ?? null,
                        'UnitPrice' => $product->UnitPrice,
                        'IsActive' => $product->IsActive
                    ];
                }),
            ]
        ]);
    }


    // Search by Subcategory Name
    public function searchBySubcategory($keyword)
    {
        $subcategory = Subcategory::where('SubcategoryName', 'LIKE', "%{$keyword}%")
            ->with([
                'category',
                'products' => function ($query) {
                    $query->with('supplier');
                }
            ])
            ->first();

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found.'], 404);
        }

        return response()->json([
            'message' => 'Subcategory search result',
            'data' => [
                'Subcategory' => $subcategory->only(['SubcategoryID', 'SubcategoryName', 'SubcategoryDescription']),
                'Category' => $subcategory->category,
                'Products' => $subcategory->products->map(function ($product) {
                    return [
                        'ProductId' => $product->ProductId,
                        'ProductName' => $product->ProductName,
                        'SupplierName' => $product->supplier->SupplierName ?? null,
                        'UnitPrice' => $product->UnitPrice,
                        'IsActive' => $product->IsActive
                    ];
                }),
            ]
        ]);
    }
}
