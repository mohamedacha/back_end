<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, Notifiable;
    protected $fillable =[
        'product_name' ,
        'img' ,
        'price' ,
        'category',
        'description',
        'quantity' ,
        'created_at',
    ];

}
