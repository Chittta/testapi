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
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'subcategory', 'supplier', 'images'])
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
                'StrickPrice' => $product->StrickPrice,
                'QuntityOnStock' => $product->QuntityOnStock,
                'QuntityOnOrcer' => $product->QuntityOnOrcer,
                'IsActive' => $product->IsActive,
                'Images' => $product->images->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'ImagePath' => url('storage/' . $img->ImagePath),
                    ];
                }),
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
        $validator = Validator::make($request->all(), [
            'ProductName' => 'required|string|max:255',
            'ProductDescription' => 'nullable|string|max:255',
            'CategoryID' => 'required|integer',
            'SubcategoryID' => 'required|integer',
            'Currency' => 'nullable|string|max:10',
            'UnitPrice' => 'nullable|numeric',
            'StrickPrice' => 'nullable|numeric',
            'PurchaseQuantity' => 'required|integer',
            'SupplierID' => 'required|integer',
            'QuntityOnStock' => 'nullable|integer',
            'QuntityOnOrcer' => 'nullable|integer',
            'IsActive' => 'nullable|boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        if (
            $request->filled('UnitPrice') &&
            $request->filled('StrickPrice') &&
            $request->StrickPrice <= $request->UnitPrice
        ) {
            return response()->json([
                'message' => 'StrickPrice must be greater than UnitPrice.'
            ], 422);
        }
        
        if ($request->CategoryID && !Category::find($request->CategoryID)) {
            return response()->json([
                'message' => 'Input category not found'
            ], 422);
        }
        if ($request->SupplierID && !Category::find($request->SupplierID)) {
            return response()->json([
                'message' => 'Input Supplier not found'
            ], 422);
        }
        if ($request->SubcategoryID && !Category::find($request->SubcategoryID)) {
            return response()->json([
                'message' => 'Input Subcategory not found'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // return $request;
            $product = Product::create($request->only([
                'ProductName',
                'ProductDescription',
                'CategoryID',
                'SubcategoryID',
                'Currency',
                'UnitPrice',
                'StrickPrice',
                'PurchaseQuantity',
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
        $product = Product::with('images')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'ProductName' => 'required|string|max:255',
            'ProductDescription' => 'nullable|string|max:255',
            'CategoryID' => 'required|integer',
            'SubcategoryID' => 'required|integer',
            'Currency' => 'nullable|string|max:10',
            'UnitPrice' => 'nullable|numeric',
            'StrickPrice' => 'nullable|numeric',
            'PurchaseQuantity' => 'required|integer',
            'SupplierID' => 'required|integer',
            'QuntityOnStock' => 'nullable|integer',
            'QuntityOnOrcer' => 'nullable|integer',
            'IsActive' => 'nullable|boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Category::find($request->CategoryID)) {
            return response()->json(['message' => 'Input category not found'], 422);
        }
        if (!Category::find($request->SubcategoryID)) {
            return response()->json(['message' => 'Input subcategory not found'], 422);
        }
        if (!Category::find($request->SupplierID)) {
            return response()->json(['message' => 'Input supplier not found'], 422);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // return $product;

        DB::beginTransaction();
        try {
            // Update product fields
            $product->update($request->only([
                'ProductName',
                'ProductDescription',
                'CategoryID',
                'SubcategoryID',
                'Currency',
                'UnitPrice',
                'StrickPrice',
                'PurchaseQuantity',
                'SupplierID',
                'QuntityOnStock',
                'QuntityOnOrcer',
                'IsActive'
            ]));

            $uploadedPaths = [];

            if ($request->hasFile('images')) {
                // Delete old images from storage and DB
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage->ImagePath);
                    $oldImage->delete();
                }

                // Upload new images
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
            }

            DB::commit();

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product->load('images')
            ]);
        } catch (Exception $err) {
            DB::rollBack();

            foreach ($uploadedPaths ?? [] as $img) {
                Storage::disk('public')->delete($img['path']);
            }

            return response()->json([
                'message' => 'Product update failed',
                'error' => $err->getMessage()
            ], 500);
        }
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
                'StrickPrice' => $product->StrickPrice,
                'PurchaseQuantity' => $product->PurchaseQuantity,
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

    public function getProductsBySubcategory($subcategoryId)
    {
        $products = Product::with('images')
            ->where('SubcategoryID', $subcategoryId)
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'No products found for this subcategory'
            ], 404);
        }

        $formattedProducts = $products->map(function ($product) {
            return [
                'ProductId' => $product->ProductId,
                'ProductName' => $product->ProductName,
                'ProductDescription' => $product->ProductDescription,
                'UnitPrice' => $product->UnitPrice,
                'IsActive' => $product->IsActive,
                'Images' => $product->images->map(function ($image) {
                    return [
                        'path' => $image->ImagePath,
                        'url' => asset('storage/' . $image->ImagePath)
                    ];
                }),
            ];
        });

        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $formattedProducts
        ]);
    }

    public function updateonly(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $product->update($request->only([
            'ProductName',
            'ProductDescription',
            'CategoryID',
            'SubcategoryID',
            'Currency',
            'UnitPrice',
            'StrickPrice',
            'PurchaseQuantity',
            'SupplierID',
            'QuntityOnStock',
            'QuntityOnOrcer'
        ]));

        return response()->json([
            'message' => 'Product Successfully Updated',
            'data' => $product
        ]);
    }
}
