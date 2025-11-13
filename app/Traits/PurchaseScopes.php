<?php

namespace App\Traits;

trait PurchaseScopes
{
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySupplier($query, int $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('purchase_date', '>=', now()->subDays($days));
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDateRange($query, ?string $from = null, ?string $to = null)
    {
        if ($from) {
            $query->whereDate('purchase_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('purchase_date', '<=', $to);
        }

        return $query;
    }

    public function scopeSearch($query, ?string $search = null)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search): void {
            $q->where('id', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['status'] ?? null, fn($q, $status) => $q->status($status))
            ->when($filters['supplier_id'] ?? null, fn($q, $supplierId) => $q->bySupplier($supplierId))
            ->when($filters['date_from'] ?? null, fn($q) => $q->dateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null))
            ->when($filters['search'] ?? null, fn($q, $search) => $q->search($search));
    }
}
