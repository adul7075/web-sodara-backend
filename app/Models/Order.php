<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
	
    protected $fillable = [
        'customer_id',
		'tanggal_transaksi',
        'length',
        'height',
		'luas',
		'initial_price_per_m',
		'final_price_per_m',
		'total_price',
		'statusjob',
		'product_id',
    ];
	
	 public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
