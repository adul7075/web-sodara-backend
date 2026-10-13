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
    Schema::create('promotions', function (Blueprint $table) {
    $table->id();

    $table->unsignedTinyInteger('cluster');

    $table->string('type'); // discount / cross

    $table->foreignId('product_id')->constrained()->cascadeOnDelete();

    $table->decimal('discount', 5, 2)->unsigned()->nullable();

    $table->foreignId('cross_product_id')
          ->nullable()
          ->constrained('products')
          ->nullOnDelete();

    $table->boolean('is_active')->default(true);

    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();

    $table->timestamps();

    $table->index('cluster');
    $table->index(['cluster','is_active']);
});

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
