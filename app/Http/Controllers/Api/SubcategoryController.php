<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subcategory;
use App\Models\Category;
class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subcategories = Subcategory::with('category')->orderBy('SubcategoryID', 'desc')->get();
        return response()->json([
            'message' => 'All subcategories',
            'count' => $subcategories->count(),
            'data' => $subcategories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'CategoryID' => 'nullable|integer',
            'SubcategoryName' => 'required|string|max:45',
            'SubcategoryDescription' => 'nullable|string|max:45',
        ]);
        if ($request->CategoryID && !Category::find($request->CategoryID)) {
            return response()->json([
                'message' => 'Inputed category not found'
            ], 422);
        }
        $subcategory = Subcategory::create($request->all());

        return response()->json([
            'message' => 'Subcategory Successfully Stored',
            'data' => $subcategory
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subcategory = Subcategory::with('category')->find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found.'], 404);
        }

        return response()->json($subcategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found.'], 404);
        }

        $subcategory->update($request->only([
            'CategoryID',
            'SubcategoryName',
            'SubcategoryDescription'
        ]));

        return response()->json([
            'message' => 'Subcategory Successfully Updated',
            'data' => $subcategory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found.'], 404);
        }

        $subcategory->delete();

        return response()->json(['message' => 'Subcategory Deleted Successfully']);
    }

    public function search($keyword)
{
    $subcategories = Subcategory::where('SubcategoryName', 'LIKE', "%{$keyword}%")->get();

    if ($subcategories->isEmpty()) {
        return response()->json(['message' => 'No subcategories found.'], 404);
    }

    return response()->json([
        'message' => 'Search results',
        'count' => $subcategories->count(),
        'data' => $subcategories
    ], 200);
}

// PATCH: Update only specific fields
public function updateonly(Request $request, $id)
{
    $subcategory = Subcategory::find($id);

    if (!$subcategory) {
        return response()->json(['message' => 'Subcategory not found.'], 404);
    }

    $subcategory->update($request->only([
        'SubcategoryName',
        'SubcategoryDescription',
        'CategoryID'
    ]));

    return response()->json([
        'message' => 'Successfully Updated',
        'data' => $subcategory
    ]);
}
}
