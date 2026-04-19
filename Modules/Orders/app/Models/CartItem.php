<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'meal_id',
        'quantity',
        'subtotal',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
