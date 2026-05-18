<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\Restaurants\Models\Restaurant;

class OrderReview extends Model
{
    use HasFactory;

    protected $table = 'order_reviews';

    protected $fillable = [
        'order_id',
        'user_id',
        'restaurant_id',
        'driver_id',
        'meals_rating',
        'driver_rating',
        'restaurant_rating',
        'comment',
    ];

    protected $casts = [
        'meals_rating' => 'integer',
        'driver_rating' => 'integer',
        'restaurant_rating' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
