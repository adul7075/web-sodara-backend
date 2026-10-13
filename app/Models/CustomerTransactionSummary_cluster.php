<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTransactionSummary_cluster extends Model
{
    protected $table = 'customer_transaction_with_clsuter';

    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $guarded = [];
}
