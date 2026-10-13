<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\Api\ClusterController;
use App\Http\Controllers\KMeansController;
use App\Http\Controllers\PromoController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


//barang
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
});
Route::get('/productsdb', [ProductController::class, 'index']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);



//pesanan
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});
Route::get('/ordersdb', [OrderController::class, 'index']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

//customer
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/customers', [CustomerController::class, 'store']);
});
Route::get('/customersdb', [CustomerController::class, 'index']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
Route::put('/customers/{id}', [CustomerController::class, 'update']);

//pesan
Route::post('/pesans', [PesanController::class, 'store']);


Route::get('/products/3', [ProductController::class, 'detaiil']);


//kmeans
Route::post('/cluster/refresh', [ClusterController::class, 'updateSummaries']);
Route::get('/cluster/results', [ClusterController::class, 'runKmeans']);

Route::middleware('auth:sanctum')->get(
    '/kmeans/clustering',
    [KMeansController::class, 'clustering']
);


Route::get('/orders/summary/customer', [OrderController::class, 'summaryPerCustomer']);
Route::get('/orders/summary/customer_cluster', [OrderController::class, 'summaryPerCustomer_cluster']);
Route::get('/kmeans/customer-segmentation', [KMeansController::class, 'customerSegmentation']);
Route::get('/kmeans/scatter', [KMeansController::class, 'customerScatterPlot']);
Route::get('/kmeans', [KMeansController::class, 'clustering']);

//promo
//Route::middleware('auth:sanctum')->post('/promo', [PromoController::class, 'store']);
Route::post('/promo', [PromoController::class, 'store']);



