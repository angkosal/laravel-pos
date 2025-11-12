<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property numeric $amount
 * @property int $order_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Payment newModelQuery()
 * @method static Builder<static>|Payment newQuery()
 * @method static Builder<static>|Payment query()
 * @method static Builder<static>|Payment whereAmount($value)
 * @method static Builder<static>|Payment whereCreatedAt($value)
 * @method static Builder<static>|Payment whereId($value)
 * @method static Builder<static>|Payment whereOrderId($value)
 * @method static Builder<static>|Payment whereUpdatedAt($value)
 * @method static Builder<static>|Payment whereUserId($value)
 * @mixin \Eloquent
 */
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function formattedAmount(): string
    {
        return number_format((float) $this->amount, 2);
    }
}
