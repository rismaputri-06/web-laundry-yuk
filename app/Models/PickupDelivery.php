<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupDelivery extends Model
{
    protected $fillable = ['order_id', 'driver_id', 'address', 'status', 'pickup_date', 'pickup_time'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}