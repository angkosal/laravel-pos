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
        'price',
        'quantity',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}
