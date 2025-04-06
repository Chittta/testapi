<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\ProductImage;


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
            'ProductName' => 'required|string|max:255',
            'ProductDescription' => 'nullable|string|max:255',
            'CategoryID' => 'required|integer',
            'SubcategoryID' => 'required|integer',
            'Currency' => 'nullable|string|max:10',
            'UnitPrice' => 'nullable|numeric',
            'SupplierID' => 'required|integer',
            'QuntityOnStock' => 'nullable|integer',
            'QuntityOnOrcer' => 'nullable|integer',
            'IsActive' => 'nullable|boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        DB::beginTransaction();
        try {
            $product = Product::create($request->only([
                'ProductName',
                'ProductDescription',
                'CategoryID',
                'SubcategoryID',
                'Currency',
                'UnitPrice',
                'SupplierID',
                'QuntityOnStock',
                'QuntityOnOrcer',
                'IsActive'
            ]));

            $uploadedPaths = [];

            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $uploadedPaths[] = [
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                ];

                ProductImage::create([
                    'ProductId' => $product->ProductId,
                    'ImagePath' => $path
                ]);
            }
            DB::commit();

            return response()->json([
                'message' => 'Product created successfully with images',
                'data' => $product->load('images')
            ], 201);
            // return response()->json([
            //     'message' => 'Product uploaded successfully',
            //     'name' => $request->input('name'),
            //     'description' => $request->input('description'),
            //     'images' => $uploadedPaths,
            // ]);
        } catch (Exception $err) {
            DB::rollBack();

            // Delete uploaded images (if any)
            foreach ($imagePaths ?? [] as $path) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'message' => 'Product creation failed',
                'error' => $err->getMessage()
            ], 500);

        }
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
