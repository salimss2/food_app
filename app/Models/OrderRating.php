<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Orders\Models\Order;
use Modules\Restaurants\Models\Restaurant;
use Modules\Auth\Models\DriverProfile;

class OrderRating extends Model
{
    use HasFactory;

    protected $table = 'order_ratings';

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
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Recalculation logic trigger on rating creation.
     */
    protected static function booted()
    {
        static::created(function ($rating) {
            static::recalculateAggregates($rating);
        });
    }

    public static function recalculateAggregates($rating)
    {
        // 1. Recalculate Restaurant aggregates
        if ($rating->restaurant_id) {
            $restaurantRatings = static::where('restaurant_id', $rating->restaurant_id)->get();
            if ($restaurantRatings->count() > 0) {
                $totalScore = $restaurantRatings->sum(function ($item) {
                    return ($item->restaurant_rating + $item->meals_rating) / 2.0;
                });
                $avgRating = round($totalScore / $restaurantRatings->count(), 2);
                $ratingCount = $restaurantRatings->count();

                $restaurant = Restaurant::find($rating->restaurant_id);
                if ($restaurant) {
                    $restaurant->update([
                        'rating_avg' => $avgRating,
                        'rating' => $avgRating,
                        'rating_count' => $ratingCount,
                    ]);
                }
            }
        }

        // 2. Recalculate Driver aggregates
        if ($rating->driver_id) {
            $driverRatings = static::where('driver_id', $rating->driver_id)
                ->whereNotNull('driver_rating')
                ->get();
            if ($driverRatings->count() > 0) {
                $avgDriverRating = round($driverRatings->avg('driver_rating'), 2);
                $driverRatingCount = $driverRatings->count();

                $driverProfile = DriverProfile::where('user_id', $rating->driver_id)->first();
                if ($driverProfile) {
                    $driverProfile->update([
                        'rating_avg' => $avgDriverRating,
                        'rating' => $avgDriverRating,
                        'rating_count' => $driverRatingCount,
                    ]);
                }
            }
        }
    }
}
