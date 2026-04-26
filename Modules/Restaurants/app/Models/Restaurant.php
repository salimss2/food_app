<?php

namespace Modules\Restaurants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Restaurants\Database\Factories\RestaurantFactory;

class Restaurant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'location',
        'status',
        'category',
        'owner_id',
        'user_id',
        'logo',
        'account_status'
    ];

    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            $path = str_contains($this->logo, '/') ? $this->logo : 'restaurants/logos/' . $this->logo;
            return asset('storage/' . $path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }


    protected $guarded = [];

    public function mealCategories()
    {
        return $this->hasMany(MealCategory::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
