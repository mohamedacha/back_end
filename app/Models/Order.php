<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory ;
    protected $fillable =[
        'confirmed' ,
        'quantity',
        'product_id',
        'user_id' ,
        'service_id',
        'created_at',
    ];
}