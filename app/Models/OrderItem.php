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
        'price' => 'float',
        'quantity' => 'integer',
    ];

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
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
