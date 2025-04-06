<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Supplier::orderByDesc('SupplierID')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'SupplierName' => 'required|string|max:45',
            'SupplierDescription' => 'nullable|string|max:100',
            'SupplierEmailId' => 'nullable|email|max:45',
            'SupplierPhone' => 'nullable|string|max:45',
            'SupplierAddress' => 'nullable|string|max:100',
        ]);

        $supplier = Supplier::create($request->all());

        return response()->json([
            'message' => 'Supplier Successfully Stored',
            'data' => $supplier
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        return response()->json($supplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $supplier->update($request->all());

        return response()->json([
            'message' => 'Supplier Successfully Updated',
            'data' => $supplier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier Successfully Deleted']);
    }
}
