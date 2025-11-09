<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property string $barcode
 * @property numeric $price
 * @property int $quantity
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $image_url
 * @method static Builder<static>|Product active()
 * @method static Builder<static>|Product bestSelling()
 * @method static Builder<static>|Product currentMonthBestSelling()
 * @method static ProductFactory factory($count = null, $state = [])
 * @method static Builder<static>|Product lowStock()
 * @method static Builder<static>|Product newModelQuery()
 * @method static Builder<static>|Product newQuery()
 * @method static Builder<static>|Product pastMonthsHotProducts()
 * @method static Builder<static>|Product query()
 * @method static Builder<static>|Product search($term)
 * @method static Builder<static>|Product whereBarcode($value)
 * @method static Builder<static>|Product whereCreatedAt($value)
 * @method static Builder<static>|Product whereDescription($value)
 * @method static Builder<static>|Product whereId($value)
 * @method static Builder<static>|Product whereImage($value)
 * @method static Builder<static>|Product whereName($value)
 * @method static Builder<static>|Product wherePrice($value)
 * @method static Builder<static>|Product whereQuantity($value)
 * @method static Builder<static>|Product whereStatus($value)
 * @method static Builder<static>|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;

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
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the product image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        }

        return asset('images/img-placeholder.jpg');
    }

    /**
     * Check if product is low stock.
     */
    public function isLowStock(): bool
    {
        return $this->quantity < 10;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity === 0;
    }

    /**
     * Scope for active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->where('quantity', '<', 10);
    }

    /**
     * Scope for best selling products (total sold > 10).
     */
    public function scopeBestSelling($query)
    {
        return $query->select(
            'products.*',
            DB::raw('SUM(order_items.quantity) as total_sold')
        )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->groupBy('products.id')
            ->having('total_sold', '>', 10)
            ->orderByDesc('total_sold')
            ->limit(10);
    }

    /**
     * Scope for current month best selling products.
     */
    public function scopeCurrentMonthBestSelling($query)
    {
        return $query->select(
            'products.*',
            DB::raw('SUM(order_items.quantity) as total_sold')
        )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereYear('orders.created_at', now()->year)
            ->whereMonth('orders.created_at', now()->month)
            ->groupBy('products.id')
            ->having('total_sold', '>', 500)
            ->orderByDesc('total_sold')
            ->limit(10);
    }

    /**
     * Scope for past months hot products (6 months).
     */
    public function scopePastMonthsHotProducts($query)
    {
        return $query->select(
            'products.*',
            DB::raw('SUM(order_items.quantity) as total_sold')
        )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->groupBy('products.id')
            ->having('total_sold', '>', 1000)
            ->orderByDesc('total_sold')
            ->limit(10);
    }

    public function scopeSearch($query, $term)
    {
        return $query->when($term, function ($query, $term): void {
            $query->where('name', 'LIKE', "%{$term}%");
        });
    }
}
