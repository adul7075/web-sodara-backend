<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW customer_transaction_summary AS
            SELECT
                c.id AS customer_id,
                c.name AS customer_name,

                COUNT(o.id) AS total_transaksi,
                SUM(o.luas) AS total_luas,
                SUM(o.total_price) AS total_spending,
                AVG(o.final_price_per_m) AS avg_price

            FROM customers c
            JOIN orders o ON o.customer_id = c.id

            GROUP BY
                c.id,
                c.name
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::statement("DROP VIEW IF EXISTS customer_transaction_summary");
    }
};
