<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
	public function index()
{
    //return response()->json(Product::all());

    // Ambil semua data dari tabel 'items'
        $productsdb = Product::all();

        // Kembalikan data dalam format JSON
        return response()->json([
            'status' => 'success',
            'data' => $productsdb
        ]);
}

	
     public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Produk berhasil disimpan',
            'product' => $product
        ], 201);
    }
	
	
	public function destroy($id)
{
    $product = Product::findOrFail($id);
    $product->delete();

    return response()->json([
        'message' => 'Produk berhasil dihapus'
    ]);
}

public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
    ]);

    return response()->json([
        'message' => 'Produk berhasil diupdate',
        'product' => $product
    ]);
}


	
}
