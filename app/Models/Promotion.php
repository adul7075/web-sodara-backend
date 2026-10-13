<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
    'cluster',
    'type',
    'product_id',
    'discount',
    'cross_product_id',
    'is_active',
    'start_date',
    'end_date'
];

public function product()
{
    return $this->belongsTo(Product::class);
}

public function crossProduct()
{
    return $this->belongsTo(Product::class, 'cross_product_id');
}


}
