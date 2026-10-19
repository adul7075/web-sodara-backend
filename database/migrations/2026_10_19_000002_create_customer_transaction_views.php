<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE VIEW customer_transaction_summary AS
            SELECT
                c.id AS customer_id,
                c.name AS customer_name,
                COUNT(o.id) AS total_transaksi,
                SUM(o.luas) AS total_luas,
                SUM(o.total_price) AS total_spending,
                AVG(o.final_price_per_m) AS avg_price
            FROM customers c
            INNER JOIN orders o ON o.customer_id = c.id
            GROUP BY c.id, c.name
        SQL);

        DB::statement(<<<'SQL'
            CREATE VIEW customer_transaction_with_cluster AS
            SELECT
                v.customer_id,
                v.customer_name,
                v.total_transaksi,
                v.total_luas,
                v.total_spending,
                v.avg_price,
                cr.cluster AS cluster
            FROM (
                SELECT
                    c.id AS customer_id,
                    c.name AS customer_name,
                    COUNT(o.id) AS total_transaksi,
                    SUM(o.luas) AS total_luas,
                    SUM(o.total_price) AS total_spending,
                    AVG(o.final_price_per_m) AS avg_price
                FROM customers c
                INNER JOIN orders o ON o.customer_id = c.id
                GROUP BY c.id, c.name
            ) v
            LEFT JOIN cluster_results cr ON cr.customer_id = v.customer_id
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS customer_transaction_with_cluster');
        DB::statement('DROP VIEW IF EXISTS customer_transaction_summary');
    }
};
