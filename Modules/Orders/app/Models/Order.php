<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Orders\Database\Factories\OrderFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_number',
        'group_id',
        'user_id',
        'restaurant_id',
        'driver_id',
        'payment_method',
        'total',
        'status',
        'scheduled_at',
        'payment_status',
        'receipt_image',
        'rejection_reason',
        'cancellation_reason',
    ];

    /**
     * العلاقة: طلب واحد يحتوي على عدة عناصر.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(\Modules\Payments\Models\Payment::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(\Modules\Restaurants\Models\Restaurant::class, 'restaurant_id');
    }

    public function driver()
    {
        return $this->belongsTo(\App\Models\User::class, 'driver_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    // protected static function newFactory(): OrderFactory
    // {
    //     // return OrderFactory::new();
    // }
}