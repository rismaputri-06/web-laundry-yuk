<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_date', 'weight', 'service_type',
        'pickup_method', 'status', 'total_price',
    ];

    public function pickupDelivery()
    {
    return $this->hasOne(PickupDelivery::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

}