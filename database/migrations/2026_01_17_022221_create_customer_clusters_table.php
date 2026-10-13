<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('customer_clusters', function (Blueprint $table) {
        $table->id();
        $table->string('customer_id');
        $table->integer('cluster');
        $table->float('pca_x');
        $table->float('pca_y');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_clusters');
    }
};
