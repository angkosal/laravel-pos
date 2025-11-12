<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $purchase_id
 * @property int $product_id
 * @property int $quantity
 * @property numeric $purchase_price Price at time of purchase
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $subtotal
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Purchase $purchase
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'purchase_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'purchase_price' => 'decimal:2'
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(related: Product::class, foreignKey: 'product_id');
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(related: Purchase::class, foreignKey: 'purchase_id');
    }

    public function getSubtotalAttribute(): float
    {
        return (float)($this->quantity * $this->purchase_price);
    }
}
