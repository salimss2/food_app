<?php

namespace Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Users\Database\Factories\FavoriteFactory;

class Favorite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'meal_id',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(\Modules\Restaurants\Models\Restaurant::class);
    }

    public function meal()
    {
        return $this->belongsTo(\Modules\Restaurants\Models\Meal::class);
    }
}
