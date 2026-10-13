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
        Schema::create('member_transactions', function (Blueprint $table) {
            $table->id();
			$table->string('nama_anggota');
			$table->integer('total_transaksi'); // How many times they shopped
			$table->integer('total_qty');       // How many items they bought
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_transactions');
    }
};
