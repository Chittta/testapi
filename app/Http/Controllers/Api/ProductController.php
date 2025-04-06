<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Supplier;
class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return response()->json(Product::orderByDesc('ProductId')->get());
        $products = Product::with(['category', 'subcategory', 'supplier'])
            ->orderByDesc('ProductId')
            ->get();

        $data = $products->map(function ($product) {
            return [
                'ProductId' => $product->ProductId,
                'ProductName' => $product->ProductName,
                'ProductDescription' => $product->ProductDescription,
                'CategoryID' => $product->CategoryID,
                'CategoryName' => $product->category->CategoryName ?? null,
                'SubcategoryID' => $product->SubcategoryID,
                'SubcategoryName' => $product->subcategory->SubcategoryName ?? null,
                'SupplierID' => $product->SupplierID,
                'SupplierName' => $product->supplier->SupplierName ?? null,
                'Currency' => $product->Currency,
                'UnitPrice' => $product->UnitPrice,
                'QuntityOnStock' => $product->QuntityOnStock,
                'QuntityOnOrcer' => $product->QuntityOnOrcer,
                'IsActive' => $product->IsActive,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Product list',
            'count' => $data->count(),
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ProductName' => 'required|string|max:45',
            'UnitPrice' => 'nullable|numeric',
            'SupplierID' => 'nullable|integer',
            'CategoryID' => 'nullable|integer',
            'SubcategoryID' => 'nullable|integer',
            'Currency' => 'nullable|string|max:45',
            'QuntityOnStock' => 'nullable|integer',
            'QuntityOnOrcer' => 'nullable|integer',
            'IsActive' => 'nullable|boolean',
        ]);

        if ($request->CategoryID && !Category::find($request->CategoryID)) {
            return response()->json([
                'message' => 'Inputed category not found'
            ], 422);
        }
        if ($request->SupplierID && !Category::find($request->SupplierID)) {
            return response()->json([
                'message' => 'Inputed Supplier not found'
            ], 422);
        }
        if ($request->SubcategoryID && !Category::find($request->SubcategoryID)) {
            return response()->json([
                'message' => 'Inputed Subcategory not found'
            ], 422);
        }

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Product Successfully Stored',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update($request->all());

        return response()->json([
            'message' => 'Product Successfully Updated',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product Successfully Deleted']);
    }

    public function search($keyword)
    {
        $products = Product::where('ProductName', 'LIKE', "%{$keyword}%")
            ->orderByDesc('ProductId')
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found.'], 404);
        }

        $data = $products->map(function ($product) {
            return [
                'ProductId' => $product->ProductId,
                'ProductName' => $product->ProductName,
                'ProductDescription' => $product->ProductDescription,
                'CategoryID' => $product->CategoryID,
                'CategoryName' => $product->category->CategoryName ?? null,
                'SubcategoryID' => $product->SubcategoryID,
                'SubcategoryName' => $product->subcategory->SubcategoryName ?? null,
                'SupplierID' => $product->SupplierID,
                'SupplierName' => $product->supplier->SupplierName ?? null,
                'Currency' => $product->Currency,
                'UnitPrice' => $product->UnitPrice,
                'QuntityOnStock' => $product->QuntityOnStock,
                'QuntityOnOrcer' => $product->QuntityOnOrcer,
                'IsActive' => $product->IsActive,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'message' => 'Search results',
            'count' => $data->count(),
            'data' => $data
        ]);
    }
}
