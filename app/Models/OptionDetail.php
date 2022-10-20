<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionDetail extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'extra_price',
        'product_option_id',
    ];

    /**
     * Get the product option that owns the option detail.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productOptions()
    {
        return $this->belongsTo(ProductOption::class);
    }

}
