<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class KMeansController extends Controller
{
    public function clustering(): JsonResponse
    {
        // ===== 1. Ambil data =====
        $data = DB::table('customer_transaction_summary')->get();
		//$data = DB::table('customer_transaction_with_cluster')->get();
		
        if ($data->count() < 3) {
            return response()->json([
                'message' => 'Data tidak cukup untuk clustering'
            ], 400);
        }

        // ===== 2. Dataset numerik =====
        $dataset = [];
        foreach ($data as $row) {
            $dataset[] = [
                'id' => $row->customer_id,
                'values' => [
                    (float) $row->total_transaksi,
                    (float) $row->total_luas,
                    (float) $row->total_spending,
                    (float) $row->avg_price
                ]
            ];
        }

        // ===== 3. Normalisasi Min-Max =====
        $normalized = $this->minMaxNormalize($dataset);

        // ===== 4. Tentukan K =====
        $k = 3;

        // ===== 5. Centroid awal (ambil data pertama) =====
        $centroids = array_slice(array_column($normalized, 'values'), 0, $k);

        $iteration = 0;
        $maxIteration = 100;
        $clusters = [];

        // ===== 6. Iterasi K-Means =====
        do {
            $iteration++;
            $oldCentroids = $centroids;
            $clusters = array_fill(0, $k, []);

            // Hitung jarak & tentukan cluster
            foreach ($normalized as $index => $point) {
                $distances = [];

                foreach ($centroids as $cIndex => $centroid) {
                    $distances[$cIndex] = $this->euclideanDistance(
                        $point['values'],
                        $centroid
                    );
                }

                $clusterIndex = array_keys($distances, min($distances))[0];
                $clusters[$clusterIndex][] = $point['values'];
            }

            // Hitung centroid baru
            foreach ($clusters as $cIndex => $cluster) {
                if (count($cluster) > 0) {
                    $centroids[$cIndex] = $this->calculateCentroid($cluster);
                }
            }

        } while ($oldCentroids != $centroids && $iteration < $maxIteration);

		// ===== 7. Simpan hasil cluster ke database =====

// Hapus hasil lama (opsional tapi biasanya perlu)
DB::table('cluster_results')->truncate();

foreach ($clusters as $clusterIndex => $cluster) {
    foreach ($cluster as $pointValues) {

        // Cari customer_id berdasarkan nilai yang cocok
        foreach ($normalized as $item) {
            if ($item['values'] === $pointValues) {

                DB::table('cluster_results')->insert([
                    'customer_id' => $item['id'],
                    'cluster' => $clusterIndex + 1, // biar mulai dari 1 bukan 0
                    'created_at' => now(),
                    //'updated_at' => now(),
                ]);
            }
        }
    }
}
		
        return response()->json([
            'iterations' => $iteration,
            'centroids' => $centroids,
            'clusters' => $clusters
        ]);
    }

    // ================= HELPER =================

    private function minMaxNormalize(array $dataset): array
    {
        $values = array_column($dataset, 'values');
        $mins = array_map('min', ...$values);
        $maxs = array_map('max', ...$values);

        foreach ($dataset as &$item) {
            foreach ($item['values'] as $i => $val) {
                $item['values'][$i] = ($maxs[$i] - $mins[$i]) != 0
                    ? ($val - $mins[$i]) / ($maxs[$i] - $mins[$i])
                    : 0;
            }
        }

        return $dataset;
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0;
        foreach ($a as $i => $val) {
            $sum += pow($val - $b[$i], 2);
        }
        return sqrt($sum);
    }

    private function calculateCentroid(array $cluster): array
    {
        $count = count($cluster);
        $dimensions = count($cluster[0]);
        $centroid = array_fill(0, $dimensions, 0);

        foreach ($cluster as $point) {
            foreach ($point as $i => $val) {
                $centroid[$i] += $val;
            }
        }

        foreach ($centroid as $i => $val) {
            $centroid[$i] = $val / $count;
        }

        return $centroid;
    }
	
	public function autoClustering(): JsonResponse
{
    $data = DB::table('customer_transaction_summary')->get();

    if ($data->count() < 3) {
        return response()->json([
            'message' => 'Data tidak cukup'
        ], 400);
    }

    // ===== Dataset =====
    $dataset = [];
    foreach ($data as $row) {
        $dataset[] = [
            'id' => $row->customer_id,
            'values' => [
                (float)$row->total_transaksi,
                (float)$row->total_luas,
                (float)$row->total_spending,
                (float)$row->avg_price
            ]
        ];
    }

    $normalized = $this->minMaxNormalize($dataset);

    $bestK = 2;
    $bestScore = -1;
    $bestClusters = [];
    $bestCentroids = [];

    // ===== LOOP K 2 sampai 6 =====
    for ($k = 2; $k <= 6; $k++) {

        $result = $this->runKMeans($normalized, $k);
        $clusters = $result['clusters'];
        $centroids = $result['centroids'];

        $score = $this->calculateSilhouette($normalized, $clusters);

        if ($score > $bestScore) {
            $bestScore = $score;
            $bestK = $k;
            $bestClusters = $clusters;
            $bestCentroids = $centroids;
        }
    }

    // ===== Simpan hasil terbaik =====
    DB::table('cluster_results')->truncate();

    foreach ($bestClusters as $clusterIndex => $cluster) {
        foreach ($cluster as $item) {
            DB::table('cluster_results')->insert([
                'customer_id' => $item['id'],
                'cluster' => $clusterIndex + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return response()->json([
        'optimal_k' => $bestK,
        'silhouette_score' => round($bestScore, 4),
        'centroids' => $bestCentroids
    ]);
}

	private function runKMeans(array $data, int $k): array
{
    $centroids = array_slice(array_column($data, 'values'), 0, $k);
    $maxIteration = 100;
    $iteration = 0;

    do {
        $iteration++;
        $oldCentroids = $centroids;
        $clusters = array_fill(0, $k, []);

        foreach ($data as $point) {
            $distances = [];

            foreach ($centroids as $cIndex => $centroid) {
                $distances[$cIndex] = $this->euclideanDistance(
                    $point['values'],
                    $centroid
                );
            }

            $clusterIndex = array_keys($distances, min($distances))[0];
            $clusters[$clusterIndex][] = $point;
        }

        foreach ($clusters as $cIndex => $cluster) {
            if (count($cluster) > 0) {
                $centroids[$cIndex] = $this->calculateCentroid(
                    array_column($cluster, 'values')
                );
            }
        }

    } while ($oldCentroids != $centroids && $iteration < $maxIteration);

    return [
        'clusters' => $clusters,
        'centroids' => $centroids
    ];
}

	private function calculateSilhouette(array $data, array $clusters): float
{
    $scores = [];

    foreach ($clusters as $clusterIndex => $cluster) {

        foreach ($cluster as $point) {

            $a = $this->averageDistance(
                $point['values'],
                array_column($cluster, 'values')
            );

            $b = PHP_FLOAT_MAX;

            foreach ($clusters as $otherIndex => $otherCluster) {

                if ($clusterIndex == $otherIndex || empty($otherCluster)) {
                    continue;
                }

                $distance = $this->averageDistance(
                    $point['values'],
                    array_column($otherCluster, 'values')
                );

                $b = min($b, $distance);
            }

            $scores[] = ($b - $a) / max($a, $b);
        }
    }

    return array_sum($scores) / count($scores);
}

	private function averageDistance(array $point, array $clusterPoints): float
{
    $total = 0;
    $count = count($clusterPoints);

    foreach ($clusterPoints as $other) {
        $total += $this->euclideanDistance($point, $other);
    }

    return $count > 0 ? $total / $count : 0;
}
	
}
