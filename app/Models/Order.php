<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id'
    ];

    protected $appends = [
        'items_count',
        'payments_count',
        'customer_name'
    ];

    /**
     * Get the order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the order payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get customer name or default text.
     */
    public function getCustomerName(): string
    {
        return $this->customer
            ? $this->customer->full_name
            : __('customer.walk_in');
    }

    /**
     * Calculate order total.
     */
    public function total(): float
    {
        return (float) $this->items->sum('price');
    }

    /**
     * Get formatted total.
     */
    public function formattedTotal(): string
    {
        return number_format($this->total(), 2);
    }

    /**
     * Calculate received amount from payments.
     */
    public function receivedAmount(): float
    {
        return (float) $this->payments->sum('amount');
    }

    /**
     * Get formatted received amount.
     */
    public function formattedReceivedAmount(): string
    {
        return number_format($this->receivedAmount(), 2);
    }

    /**
     * Calculate remaining balance.
     */
    public function remainingBalance(): float
    {
        return $this->total() - $this->receivedAmount();
    }

    /**
     * Check if order is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->receivedAmount() >= $this->total();
    }

    /**
     * Scope for orders with a specific customer.
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope for orders by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
    }
}
