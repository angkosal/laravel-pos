<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    use HasFactory, SoftDeletes;

    const PAYMENT_PENDING = 1, PAYMENT_FAILURE = 2, PAYMENT_SUCCESS = 3;

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

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function orderDetails(){
        return $this->hasMany(OrderDetail::class);
    }

    public function getStatusString(){
        switch($this->status){
            case Order::PAYMENT_PENDING: return 'Payment Pending';
            case Order::PAYMENT_FAILURE: return 'Payment Failure';
            case Order::PAYMENT_SUCCESS: return 'Payment Success';
            default: return 'Undefined';
        }
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCustomerName()
    {
        if($this->customer) {
            return $this->customer->first_name . ' ' . $this->customer->last_name;
        }
        return 'Working Customer';
    }

    public function total()
    {
        return $this->items->map(function ($i){
            return $i->price;
        })->sum();
    }

    public function formattedTotal()
    {
        return number_format($this->total(), 2);
    }

    public function receivedAmount()
    {
        return $this->payments->map(function ($i){
            return $i->amount;
        })->sum();
    }

    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }
}
