<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unique();
            
            // Fitur Utama untuk K-Means
            $table->integer('total_frequency')->default(0); // Berapa kali belanja
            $table->decimal('total_monetary', 15, 2)->default(0); // Total uang (total_price)
            $table->decimal('total_luas', 10, 2)->default(0); // Total volume/luas produk
            
            // Fitur tambahan untuk analisis Recency
            $table->date('last_transaction_date')->nullable();
            
            // Hasil Cluster
            $table->integer('cluster')->nullable(); // 0, 1, atau 2
            $table->string('label')->nullable(); // misal: 'Loyal', 'Passive', dll
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_summaries');
    }
};