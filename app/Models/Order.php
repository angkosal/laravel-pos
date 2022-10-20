<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    use HasFactory, SoftDeletes;

    /**
     * constant variable for order status.
     *
     * @var int
     */
    const PAYMENT_PENDING = 1, PAYMENT_FAILURE = 2, PAYMENT_SUCCESS = 3, PICKUP_PARTIALLY = 4, PICKUP_ALL = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'pick_up_start',
        'pick_up_end',
        'total_price',
        'status',
        'is_sandbox_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pick_up_start' => 'datetime',
        'pick_up_end' => 'datetime',
    ];

    /**
     * Get the student that owns the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the order details for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Get the payments for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the order status in string.
     *
     * @return string
     */
    public function getStatusString()
    {
        return match ($this->status) {
            Order::PAYMENT_PENDING => 'Payment Pending',
            Order::PAYMENT_FAILURE => 'Payment Failure',
            Order::PAYMENT_SUCCESS => 'Payment Success',
            Order::PICKUP_PARTIALLY => 'Partially Picked Up',
            Order::PICKUP_ALL => 'All Picked Up',
            default => 'Undefined',
        };
    }

}
