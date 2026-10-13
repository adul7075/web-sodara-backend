<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;

class PromoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cluster' => 'required|integer|min:0|max:10',
            'type' => 'required|in:discount,cross',

            'product_id' => 'required|exists:products,id',

            'discount' => 'nullable|numeric|min:0|max:100',

            'cross_product_id' => 'nullable|exists:products,id',

            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // rule logic
        if ($validated['type'] === 'discount' && empty($validated['discount'])) {
            return response()->json([
                'error' => 'Discount wajib diisi untuk tipe discount'
            ], 422);
        }

        if ($validated['type'] === 'cross' && empty($validated['cross_product_id'])) {
            return response()->json([
                'error' => 'Cross product wajib diisi untuk tipe cross'
            ], 422);
        }

        Promotion::create([
            'cluster' => $validated['cluster'],
            'type' => $validated['type'],
            'product_id' => $validated['product_id'],
            'discount' => $validated['discount'] ?? null,
            'cross_product_id' => $validated['cross_product_id'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        return response()->json([
            'message' => 'Promo berhasil disimpan'
        ], 201);
    }
}
