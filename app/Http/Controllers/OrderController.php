<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\CustomerTransactionSummary;
use App\Models\CustomerTransactionSummary_cluster;

class OrderController extends Controller
{
	public function index()
{
    //return response()->json(Order::all());

    // Ambil semua data dari tabel 'items'
        $ordersdb = Order::all();

        // Kembalikan data dalam format JSON
        return response()->json([
            'status' => 'success',
            'data' => $ordersdb
        ]);
}

	
     public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|string|max:255',
			'tanggal_transaksi' => 'required|string|max:255',
            'length' => 'nullable|string',
            'height' => 'required|string',
			'luas' => 'required|string',
			'initial_price_per_m' => 'required|string',
			'final_price_per_m' => 'required|string',
			'total_price' => 'required|string',
			'statusjob' => 'required|string',
			'product_id' => 'required|string',
        ]);

        $order = Order::create($validated);

        return response()->json([
            'message' => 'Produk berhasil disimpan',
            'order' => $order
        ], 201);
    }
	
	public function destroy($id)
{
    $order = Order::findOrFail($id);
    $order->delete();

    return response()->json([
        'message' => 'Transaksi berhasil dihapus'
    ]);
}

	public function update(Request $request, $id)
{
    $order = Order::findOrFail($id);

    $order->update([
        'customer_id' => $request->customer_id,
        'tanggal_transaksi' => $request->tanggal_transaksi,
        'length' => $request->length,
        'height' => $request->height,
	'luas' => $request->luas,
        'initial_price_per_m' => $request->initial_price_per_m,
        'final_price_per_m' => $request->final_price_per_m,
        'total_price' => $request->total_price,
	'statusjob' => $request->statusjob,
        'product_id' => $request->product_id,
    ]);

    return response()->json([
        'message' => 'Transaksi berhasil diupdate',
        'data' => $order
    ]);
}

public function summaryPerCustomer()
{
    return response()->json([
        'status' => 'success',
        'data' => CustomerTransactionSummary::all()
    ]);
}

public function summaryPerCustomer_cluster()
{
    return response()->json([
        'status' => 'success',
        'data' => CustomerTransactionSummary_cluster::all()
    ]);
}
	
}
