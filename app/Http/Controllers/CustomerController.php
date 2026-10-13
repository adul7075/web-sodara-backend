<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
	public function index()
{
    //return response()->json(Customer::all());

    // Ambil semua data dari tabel 'items'
        $customersdb = Customer::all();

        // Kembalikan data dalam format JSON
        return response()->json([
            'status' => 'success',
            'data' => $customersdb
        ]);
}

	
     public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'required|string',
            'customer_type' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'message' => 'Customer berhasil disimpan',
            'customer' => $customer
        ], 201);
    }
	public function destroy($id)
{
    $customer = Customer::findOrFail($id);
    $customer->delete();

    return response()->json([
        'message' => 'Customer berhasil dihapus'
    ]);
}

public function update(Request $request, $id)
{
    $customer = Customer::findOrFail($id);

    $customer->update([
        'name' => $request->name,
        'phone' => $request->phone,
        'address' => $request->address,
        'customer_type' => $request->customertype,
    ]);

    return response()->json([
        'message' => 'Customer berhasil diupdate',
        'data' => $customer
    ]);
}


	
}
