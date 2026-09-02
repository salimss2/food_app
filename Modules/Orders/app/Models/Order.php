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
        'coupon_code',
        'discount_amount',
        'total',
        'total_price',
        'status',
        'scheduled_at',
        'customer_notes',
        'payment_status',
        'receipt_image',
        'rejection_reason',
        'cancellation_reason',
        'latitude',
        'longitude',
        'driver_earning',
        'delivery_distance',
        'delivery_fee',
        'driver_commission',
        'platform_commission',
        'delivery_lat',
        'delivery_lng',
        'settlement_id',
        'restaurant_settlement_id',
        'accepted_at',
        'confirmed_at',
        'ready_at',
        'delivered_at',
    ];

    public function review()
    {
        return $this->hasOne(OrderReview::class, 'order_id');
    }

    public function rating()
    {
        return $this->hasOne(\App\Models\OrderRating::class, 'order_id');
    }

    /**
     * العلاقة: الطلب ينتمي لتسوية مالية واحدة.
     */
    public function settlement()
    {
        return $this->belongsTo(Settlement::class, 'settlement_id');
    }

    /**
     * Relationship: Order belongs to a restaurant settlement.
     */
    public function restaurantSettlement()
    {
        return $this->belongsTo(RestaurantSettlement::class, 'restaurant_settlement_id');
    }

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

    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    // protected static function newFactory(): OrderFactory
    // {
    //     // return OrderFactory::new();
    // }
    /**
     * علاقة الطلب بمهمة التوصيل (للسائق)
     */
    public function deliveryTask()
    {
        // الطلب الواحد له مهمة توصيل واحدة
        return $this->hasOne(\Modules\Delivery\Models\DeliveryTask::class, 'order_id');
    }

    /**
     * Get all sibling sub-orders created in the same multi-vendor checkout.
     */
    public function groupSiblingOrders()
    {
        return $this->hasMany(Order::class, 'group_id', 'group_id')
                    ->where('id', '!=', $this->id);
    }

    /**
     * Check if the order is part of a multi-restaurant order group.
     */
    public function isMultiVendorOrder(): bool
    {
        return !empty($this->group_id) && Order::where('group_id', $this->group_id)->count() > 1;
    }

    /**
     * Get the total financial sum of the entire order group.
     */
    public function getGroupGrandTotalAttribute(): float
    {
        if (empty($this->group_id)) {
            return (float) ($this->total ?? $this->total_price ?? 0);
        }
        return (float) Order::where('group_id', $this->group_id)
            ->selectRaw('SUM(COALESCE(total, total_price, 0)) as total_sum')
            ->value('total_sum');
    }
}
