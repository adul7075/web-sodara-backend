<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClusterController extends Controller
{
    // Fungsi untuk memproses data mentah ke tabel summary (Agregasi)
    public function updateSummaries()
    {
        try {
            DB::table('customer_summaries')->truncate();

            // Ambil data dari tabel transaksi (sesuai file CSV kamu)
            $aggregations = DB::table('orders')
                ->select(
                    'customer_id',
                    DB::raw('COUNT(id) as total_frequency'),
                    DB::raw('SUM(total_price) as total_monetary'),
                    DB::raw('SUM(CAST(REPLACE(luas, ",", ".") AS DECIMAL(10,2))) as total_luas'),
                    DB::raw('MAX(tanggal_transaksi) as last_transaction_date')
                )
                ->where('statusjob', 'completed')
                ->groupBy('customer_id')
                ->get();

            foreach ($aggregations as $row) {
                DB::table('customer_summaries')->insert([
                    'customer_id' => $row->customer_id,
                    'total_frequency' => $row->total_frequency,
                    'total_monetary' => $row->total_monetary,
                    'total_luas' => $row->total_luas,
                    'last_transaction_date' => $row->last_transaction_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['message' => 'Data summary berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Fungsi untuk menjalankan K-Means dan mengembalikan JSON
    public function runKmeans()
    {
        $dataset = DB::table('customer_summaries')->get();
        
        if ($dataset->isEmpty()) {
            return response()->json(['message' => 'Data kosong, jalankan updateSummaries dulu'], 404);
        }

        // ... Logika K-Means kamu di sini ...
        // (Gunakan data dari $dataset untuk koordinat X dan Y)

        return response()->json([
            'status' => 'success',
            'data' => $dataset // Hasil yang sudah ada label clusternya
        ]);
    }
}