<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'barcode',
        'purchase_price',
        'selling_price',
        'quantity',
        'status'
    ];
}
