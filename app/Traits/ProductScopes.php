<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ProductScopes
{
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
