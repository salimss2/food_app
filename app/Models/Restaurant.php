<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Modules\Restaurants\Models\MealCategory; // Add this import

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'description',
        'address',
        'phone',
        'status',
        'rating'
    ];
    public function meal_categories()
    {
        return $this->hasMany(MealCategory::class);
    }
}