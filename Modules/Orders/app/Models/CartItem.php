<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Restaurants\Models\Meal;
use Modules\Restaurants\Models\Offer;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'meal_id',
        'offer_id',
        'customizations',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'customizations' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class, 'meal_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
}
