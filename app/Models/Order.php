<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable =[
        'confirmed' ,
        'quantity',
        'product_id',
        'user_id' ,
        'service_id',
        'created_at',
    ];
}