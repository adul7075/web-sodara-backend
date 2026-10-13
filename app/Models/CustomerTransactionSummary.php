<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTransactionSummary extends Model
{
    //protected $table = 'customer_transaction_with_cluster';
	protected $table = 'customer_transaction_with_cluster';

    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $guarded = [];
}
