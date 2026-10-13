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
		Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
	$table->date('tanggal_transaksi'); // meter

    $table->float('length'); // meter
    $table->float('height'); // meter
    $table->float('luas');   // length * height

    $table->integer('initial_price_per_m'); // 800.000
    $table->integer('final_price_per_m')->nullable(); // harga deal 750.000
    $table->integer('total_price')->nullable();

    $table->enum('statusjob', [
        'pending',
        'survey',
        'price_fixation',
        'material_buy',
        'in_production',
        'progress_reported',
        'installation',
        'completed'
    ])->default('pending');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
