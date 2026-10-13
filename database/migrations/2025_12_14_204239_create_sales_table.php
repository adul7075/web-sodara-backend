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
        /**Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });*/
		Schema::create('sales', function (Blueprint $table) {
		$table->id();
		$table->foreignId('customer_id')->constrained()->onDelete('cascade');
		$table->integer('total_transaction');
		$table->double('total_amount');
		$table->integer('frequency');
		$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
