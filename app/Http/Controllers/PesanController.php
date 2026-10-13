<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pesan;

class PesanController extends Controller
{
    //
	
	public function index()
{
    //return response()->json(Pesan::all());

    // Ambil semua data dari tabel 'items'
        $pesansdb = Pesan::all();

        // Kembalikan data dalam format JSON
        return response()->json([
            'status' => 'success',
            'data' => $pesansdb
        ]);
}

	
     public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email'=> 'required|string',
            'pesan' => 'nullable|string',
        ]);

        $pesan = Pesan::create($validated);

        return response()->json([
            'message' => 'Pesan berhasil disimpan',
            'pesan' => $pesan
        ], 201);
    }

}
