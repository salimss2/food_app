<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Orders\Database\Factories\OrderItemFactory;
use Modules\Restaurants\Models\Meal;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'meal_id',
        'offer_id',
        'type',
        'combo_meals',
        'name',
        'quantity',
        'price',
        'subtotal',
        'customizations',
        'special_instructions',
    ];

    protected $casts = [
        'customizations' => 'array',
        'combo_meals' => 'array',
    ];

    /**
     * العلاقة: عنصر الطلب ينتمي لطلب واحد.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * العلاقة: عنصر الطلب ينتمي لوجبة واحدة.
     */
    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    /**
     * العلاقة: عنصر الطلب ينتمي لعرض كومبو واحد.
     */
    public function offer()
    {
        return $this->belongsTo(\Modules\Restaurants\Models\Offer::class);
    }
}