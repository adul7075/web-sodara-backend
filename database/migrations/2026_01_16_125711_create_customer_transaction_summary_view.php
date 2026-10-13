<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW customer_transaction_summary AS
            SELECT
                customer_id,
                COUNT(*) AS total_transaksi,
                SUM(luas) AS total_luas,
                SUM(total_price) AS total_spending,
                AVG(final_price_per_m) AS avg_price
            FROM orders
            GROUP BY customer_id
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS customer_transaction_summary");
    }
};
