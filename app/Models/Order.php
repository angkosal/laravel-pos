<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $customer_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Customer|null $customer
 * @property-read Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Order byCustomer($customerId)
 * @method static Builder<static>|Order dateRange($startDate, string $endDate)
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static Builder<static>|Order newModelQuery()
 * @method static Builder<static>|Order newQuery()
 * @method static Builder<static>|Order query()
 * @method static Builder<static>|Order whereCreatedAt($value)
 * @method static Builder<static>|Order whereCustomerId($value)
 * @method static Builder<static>|Order whereId($value)
 * @method static Builder<static>|Order whereUpdatedAt($value)
 * @method static Builder<static>|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id'
    ];

    /**
     * Get the order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * Get the order payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * Get the user who created the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get customer name or default text.
     */
    public function getCustomerName(): string
    {
        return $this->customer
            ? "{$this->customer->first_name} {$this->customer->last_name}"
            : __('walk_in');
    }

    /**
     * Calculate order total.
     */
    public function total(): float
    {
        if ($this->relationLoaded('items')) {
            return (float) $this->items->sum('price');
        }
        return (float) $this->items()->sum('price');
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
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum(fn($payment): float => (float) $payment->amount);
        }
        return (float) $this->payments()->sum('amount');
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
    public function scopeDateRange($query, $startDate, string $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
    }
}
