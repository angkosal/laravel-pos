<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'amount',
        'order_id',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function getAmountAttribute($value): float
    {
        return (float) $value;
    }

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(related: Order::class, foreignKey: 'order_id');
    }

    /**
     * Get the user who made the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'order_id');
    }

    /**
     * Get formatted amount.
     */
    public function formattedAmount(): string
    {
        return number_format($this->amount, 2);
    }
}
