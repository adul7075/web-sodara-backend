<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
{
    return response()->json(Order::all());
}

	
     public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pelanggan' => 'nullable|string',
            'price' => 'required|numeric',
        ]);

        $order = Order::create($validated);

        return response()->json([
            'message' => 'Produk berhasil disimpan',
            'order' => $order
        ], 201);
    }
}
