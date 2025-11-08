<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'price',
        'quantity',
        'product_id',
        'order_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function getPriceAttribute($value): float
    {
        return (float) $value;
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get subtotal for this item.
     */
    public function subtotal(): float
    {
        return $this->price;
    }

    /**
     * Get unit price.
     */
    public function unitPrice(): float
    {
        return $this->quantity > 0 ? $this->price / $this->quantity : 0;
    }
}
