<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 1, STATUS_FAILURE = 2, STATUS_ABORT = 3, STATUS_SUCCESS = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'payment_type_id',
        'payment_detail_2c2p_id',
        'payment_detail_stripe_id',
        'amount',
        'status',
        'is_sandbox_payment',
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function paymentType(){
        return $this->belongsTo(PaymentType::class);
    }
}
