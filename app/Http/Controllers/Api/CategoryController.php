<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('CategoryID', 'desc')->get();
        return response()->json([
            'message' => "Search Result",
            'count' => count($categories),
            'data' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'CategoryName' => 'required|string|max:45',
            'CategoryDescription' => 'nullable|string|max:45',
        ]);

        $category = Category::create($request->all());
        return response()->json([
            'message' => 'New Category Add Successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'CategoryName' => 'required|string|max:45',
            'CategoryDescription' => 'nullable|string|max:45',
        ]);

        $category->update($request->all());
        return response()->json([
            'message' => 'Category Update Successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }

    public function search($keyword)
    {
        $categories = Category::where('CategoryName', 'LIKE', "%{$keyword}%")->get();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found.'], 404);
        }

        return response()->json([
            'message' => 'Search results',
            'count' => $categories->count(),
            'data' => $categories
        ], 200);
    }

    public function updateonly(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $category->update($request->only([
            'CategoryName',
            'CategoryDescription'
        ]));

        return response()->json([
            'message' => 'Successfully Updated',
            'data' => $category
        ]);
    }

}
