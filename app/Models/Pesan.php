<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pesan extends Model
{
    use HasFactory;
	
    protected $fillable = [
        'name',
        'phone',
        'email',
		'pesan'
    ];
	
	public function order()
    {
        return $this->hasMany(Order::class);
    }
}
