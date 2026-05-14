<?php

namespace Modules\Delivery\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Orders\Models\Order;
use App\Models\User;

class DeliveryTask extends Model
{
    protected $table = 'delivery'; 

    protected $fillable = [
        'order_id', 
        'driver_id', 
        'status', 
        'pickup_time', 
        'delivery_time'
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'delivery_time' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}