<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'media_path',
        'barcode',
        'price',
        'status',
        'store_id',
        'category_id',
    ];

    public function Store(){
        return $this->belongsTo(Store::class);
    }

    public function productCategory(){
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
