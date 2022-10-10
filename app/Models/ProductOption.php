<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOption extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'product_id',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function optionDetails(){
        return $this->hasMany(OptionDetail::class);
    }
}
