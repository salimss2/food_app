<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Orders\Database\Factories\CartFactory;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'total'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // protected static function newFactory(): CartFactory
    // {
    //     // return CartFactory::new();
    // }
}